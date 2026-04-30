@props(['variant' => 'primary'])

@php
    $base = "inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-1";

    $variants = [
        'primary'   => 'bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white focus:ring-emerald-300 shadow-sm shadow-emerald-100',
        'secondary' => 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 focus:ring-slate-200',
        'danger'    => 'bg-rose-500 hover:bg-rose-600 active:bg-rose-700 text-white focus:ring-rose-300 shadow-sm shadow-rose-100',
        'outline'   => 'bg-transparent border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-200',
        'ghost'     => 'bg-transparent text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-200',
        'outline-blue' => 'bg-transparent border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-200',
    ];

    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

<button {{ $attributes->merge(['class' => "$base $classes"]) }}>
    {{ $slot }}
</button>
