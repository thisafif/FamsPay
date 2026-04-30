@props(['type' => 'warning'])

@php
    $styles = [
        'warning' => [
            'wrap'  => 'bg-amber-50 border-amber-200 text-amber-800',
            'icon'  => 'bg-amber-100 text-amber-500',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>',
        ],
        'success' => [
            'wrap'  => 'bg-emerald-50 border-emerald-200 text-emerald-800',
            'icon'  => 'bg-emerald-100 text-emerald-500',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        ],
        'danger' => [
            'wrap'  => 'bg-rose-50 border-rose-200 text-rose-800',
            'icon'  => 'bg-rose-100 text-rose-500',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>',
        ],
        'info' => [
            'wrap'  => 'bg-sky-50 border-sky-200 text-sky-800',
            'icon'  => 'bg-sky-100 text-sky-500',
            'svg'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>',
        ],
    ];

    $style = $styles[$type] ?? $styles['warning'];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-2xl border {$style['wrap']}"]) }}>
    <div class="flex-shrink-0 w-8 h-8 rounded-xl {$style['icon']} flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            {!! $style['svg'] !!}
        </svg>
    </div>
    <div class="text-sm font-medium leading-relaxed pt-1">
        {{ $slot }}
    </div>
</div>
