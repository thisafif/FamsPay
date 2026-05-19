<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Laravel\Connection;
use Illuminate\Support\Facades\DB;

final class CreateMongoIndexes extends Command
{
    protected $signature   = 'mongodb:create-indexes';
    protected $description = 'Buat semua index MongoDB sesuai rekomendasi Project Context section 14';

    public function handle(): int
    {
        $this->info('Membuat MongoDB indexes...');

        /** @var Connection $db */
        $db = DB::connection('mongodb');

        // ── users ────────────────────────────────────────────────
        $this->createIndexes($db, 'users', [
            [
                'key'    => ['email' => 1],
                'name'   => 'users_email_unique',
                'unique' => true,
            ],
            [
                'key'  => ['family_id' => 1],
                'name' => 'users_family_id',
            ],
            [
                'key'  => ['family_id' => 1, 'role' => 1],
                'name' => 'users_family_id_role',
            ],
        ]);

        // ── families ─────────────────────────────────────────────
        $this->createIndexes($db, 'families', [
            [
                'key'    => ['join_code' => 1],
                'name'   => 'families_join_code_unique',
                'unique' => true,
            ],
        ]);

        // ── transactions ─────────────────────────────────────────
        $this->createIndexes($db, 'transactions', [
            [
                'key'  => ['user_id' => 1, 'txn_date' => -1],
                'name' => 'transactions_user_id_txn_date',
            ],
            [
                'key'  => ['family_id' => 1, 'txn_date' => -1],
                'name' => 'transactions_family_id_txn_date',
            ],
            [
                'key'    => ['goal_id' => 1],
                'name'   => 'transactions_goal_id',
                'sparse' => true, // hanya index dokumen yang punya goal_id (tidak null)
            ],
            [
                'key'  => ['is_deleted' => 1, 'txn_date' => -1],
                'name' => 'transactions_is_deleted_txn_date',
            ],
        ]);

        // ── goals ────────────────────────────────────────────────
        $this->createIndexes($db, 'goals', [
            [
                'key'  => ['user_id' => 1, 'status' => 1],
                'name' => 'goals_user_id_status',
            ],
            [
                'key'  => ['family_id' => 1],
                'name' => 'goals_family_id',
            ],
        ]);

        // ── user_monthly_limits ───────────────────────────────────
        $this->createIndexes($db, 'user_monthly_limits', [
            [
                'key'    => ['user_id' => 1, 'period_month' => 1],
                'name'   => 'uml_user_id_period_month_unique',
                'unique' => true,
            ],
            [
                'key'  => ['family_id' => 1, 'period_month' => 1],
                'name' => 'uml_family_id_period_month',
            ],
        ]);

        $this->info('Semua index berhasil dibuat.');

        return self::SUCCESS;
    }

    /**
     * Buat index pada satu collection, skip jika sudah ada.
     *
     * @param  array<int, array<string, mixed>>  $indexes
     */
    private function createIndexes(Connection $db, string $collection, array $indexes): void
    {
        $this->line("  → Collection: <comment>{$collection}</comment>");

        foreach ($indexes as $index) {
            $key     = $index['key'];
            $options = array_filter([
                'name'   => $index['name']   ?? null,
                'unique' => $index['unique'] ?? null,
                'sparse' => $index['sparse'] ?? null,
            ], fn ($v) => $v !== null);

            try {
                $db->getCollection($collection)->createIndex($key, $options);
                $this->line("    ✓ {$options['name']}");
            } catch (\Exception $e) {
                // Index sudah ada — skip
                if (str_contains($e->getMessage(), 'already exists')) {
                    $this->line("    ~ {$options['name']} (sudah ada, skip)");
                } else {
                    $this->error("    ✗ {$options['name']}: {$e->getMessage()}");
                }
            }
        }
    }
}
