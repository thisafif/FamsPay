<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\MonthlyLimitRepository;
use Carbon\Carbon;

final class DashboardService
{
    public function __construct(
        private readonly MonthlyLimitRepository $limits,
    ) {}

    /**
     * Ambil data personal dashboard untuk user yang sedang login.
     * Semua data di-scope by user_id — tidak ada data user lain.
     * Soft deleted transactions tidak dihitung.
     *
     * @return array{
     *   wallet_balance: int,
     *   current_month: array,
     *   total_savings: int,
     *   flow_analysis: array,
     *   budget_analysis: array|null,
     *   recent_transactions: array,
     * }
     */
    public function getPersonalDashboard(User $user): array
    {
        $userId  = (string) $user->getKey();
        $period  = now()->format('Y-m');
        $start   = Carbon::now()->startOfMonth();
        $end     = Carbon::now()->endOfMonth();

        // ── Monthly income & expense (dari transaksi aktif bulan ini) ──
        $monthlyTxns = Transaction::where('user_id', $userId)
            ->where('is_deleted', false)
            ->whereBetween('txn_date', [$start, $end])
            ->get(['type', 'amount']);

        $monthlyIncome  = $monthlyTxns->where('type', 'income')->sum('amount');
        $monthlyExpense = $monthlyTxns->where('type', 'expense')->sum('amount');

        // ── Total tabungan pribadi (sum current_amount semua goal user) ──
        $totalSavings = Goal::where('user_id', $userId)
            ->sum('current_amount');

        // ── Flow analysis — net flow bulan ini ──
        $netFlow = $monthlyIncome - $monthlyExpense;

        $flowAnalysis = [
            'income'   => (int) $monthlyIncome,
            'expense'  => (int) $monthlyExpense,
            'net_flow' => (int) $netFlow,
            'period'   => $period,
        ];

        // ── Budget analysis — dari monthly limit record ──
        $limitRecord  = $this->limits->findByUserAndPeriod($userId, $period);
        $budgetAnalysis = null;

        if ($limitRecord) {
            $usagePercent = $limitRecord->monthly_limit_base > 0
                ? round(($limitRecord->total_expense_in_period / $limitRecord->monthly_limit_base) * 100, 1)
                : 0;

            $budgetAnalysis = [
                'monthly_limit_base'      => (int) $limitRecord->monthly_limit_base,
                'remaining_limit'         => (int) $limitRecord->remaining_limit,
                'total_expense_in_period' => (int) $limitRecord->total_expense_in_period,
                'usage_percent'           => $usagePercent,
                'period'                  => $limitRecord->period_month,
            ];
        }

        // ── Recent transactions — 5 transaksi terbaru user ──
        $recentTransactions = Transaction::where('user_id', $userId)
            ->where('is_deleted', false)
            ->orderBy('txn_date', 'desc')
            ->limit(5)
            ->get(['_id', 'type', 'amount', 'txn_date', 'category_name', 'note', 'is_system'])
            ->map(fn ($txn) => [
                'id'            => (string) $txn->getKey(),
                'type'          => $txn->type,
                'amount'        => (int) $txn->amount,
                'txn_date'      => $txn->txn_date?->toIso8601String(),
                'category_name' => $txn->category_name,
                'note'          => $txn->note,
                'is_system'     => (bool) $txn->is_system,
            ])
            ->values()
            ->toArray();

        return [
            'wallet_balance'      => (int) $user->wallet_balance,
            'current_month'       => [
                'income'  => (int) $monthlyIncome,
                'expense' => (int) $monthlyExpense,
                'period'  => $period,
            ],
            'total_savings'       => (int) $totalSavings,
            'flow_analysis'       => $flowAnalysis,
            'budget_analysis'     => $budgetAnalysis,
            'recent_transactions' => $recentTransactions,
        ];
    }

    /**
     * Ambil data family dashboard — hanya untuk admin.
     * Semua data di-scope by family_id.
     * Soft deleted transactions tidak dihitung.
     * Detail goals anggota lain tidak dibuka.
     *
     * @throws \DomainException jika user tidak punya family
     *
     * @return array{
     *   total_family_wallet_balance: int,
     *   current_month: array,
     *   total_family_savings: int,
     *   flow_analysis: array,
     *   budget_analysis: array,
     *   recent_transactions: array,
     *   members_summary: array,
     * }
     */
    public function getFamilyDashboard(User $admin): array
    {
        if (!$admin->family_id) {
            throw new \DomainException('You do not belong to any family.');
        }

        $familyId = (string) $admin->family_id;
        $period   = now()->format('Y-m');
        $start    = Carbon::now()->startOfMonth();
        $end      = Carbon::now()->endOfMonth();

        // ── Total wallet balance seluruh anggota family ──
        $members            = \App\Models\User::where('family_id', $familyId)->get();
        $totalFamilyBalance = $members->sum('wallet_balance');

        // ── Monthly income & expense family bulan ini ──
        $monthlyTxns = Transaction::where('family_id', $familyId)
            ->where('is_deleted', false)
            ->whereBetween('txn_date', [$start, $end])
            ->get(['type', 'amount']);

        $monthlyIncome  = $monthlyTxns->where('type', 'income')->sum('amount');
        $monthlyExpense = $monthlyTxns->where('type', 'expense')->sum('amount');

        // ── Total tabungan keluarga (agregat current_amount semua goal semua anggota) ──
        $memberIds          = $members->map(fn ($m) => (string) $m->getKey())->toArray();
        $totalFamilySavings = Goal::whereIn('user_id', $memberIds)->sum('current_amount');

        // ── Flow analysis family ──
        $netFlow      = $monthlyIncome - $monthlyExpense;
        $flowAnalysis = [
            'income'   => (int) $monthlyIncome,
            'expense'  => (int) $monthlyExpense,
            'net_flow' => (int) $netFlow,
            'period'   => $period,
        ];

        // ── Budget analysis — agregat semua monthly limit record family bulan ini ──
        $limitRecords      = \App\Models\UserMonthlyLimit::where('family_id', $familyId)
            ->where('period_month', $period)
            ->get();

        $totalLimitBase    = $limitRecords->sum('monthly_limit_base');
        $totalRemaining    = $limitRecords->sum('remaining_limit');
        $totalExpenseLimit = $limitRecords->sum('total_expense_in_period');
        $usagePercent      = $totalLimitBase > 0
            ? round(($totalExpenseLimit / $totalLimitBase) * 100, 1)
            : 0;

        $budgetAnalysis = [
            'total_monthly_limit_base' => (int) $totalLimitBase,
            'total_remaining_limit'    => (int) $totalRemaining,
            'total_expense_in_period'  => (int) $totalExpenseLimit,
            'usage_percent'            => $usagePercent,
            'period'                   => $period,
            'members_with_limit'       => $limitRecords->count(),
        ];

        // ── Recent family transactions — 10 terbaru ──
        $recentTransactions = Transaction::where('family_id', $familyId)
            ->where('is_deleted', false)
            ->orderBy('txn_date', 'desc')
            ->limit(10)
            ->get(['_id', 'user_id', 'type', 'amount', 'txn_date', 'category_name', 'note', 'is_system'])
            ->map(fn ($txn) => [
                'id'            => (string) $txn->getKey(),
                'user_id'       => (string) $txn->user_id,
                'type'          => $txn->type,
                'amount'        => (int) $txn->amount,
                'txn_date'      => $txn->txn_date?->toIso8601String(),
                'category_name' => $txn->category_name,
                'note'          => $txn->note,
                'is_system'     => (bool) $txn->is_system,
            ])
            ->values()
            ->toArray();

        // ── Members summary — wallet balance per anggota (tanpa detail goals) ──
        $membersSummary = $members->map(fn ($member) => [
            'user_id'            => (string) $member->getKey(),
            'full_name'          => $member->full_name,
            'role'               => $member->role,
            'wallet_balance'     => (int) $member->wallet_balance,
            // goals tidak dibuka — hanya jumlah goal aktif
            'active_goals_count' => Goal::where('user_id', (string) $member->getKey())
                ->where('status', 'active')
                ->count(),
        ])->values()->toArray();

        return [
            'total_family_wallet_balance' => (int) $totalFamilyBalance,
            'current_month'               => [
                'income'  => (int) $monthlyIncome,
                'expense' => (int) $monthlyExpense,
                'period'  => $period,
            ],
            'total_family_savings'        => (int) $totalFamilySavings,
            'flow_analysis'               => $flowAnalysis,
            'budget_analysis'             => $budgetAnalysis,
            'recent_transactions'         => $recentTransactions,
            'members_summary'             => $membersSummary,
        ];
    }
}
