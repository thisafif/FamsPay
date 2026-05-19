@props([
    'padding' => 'p-6',
    'noPadding' => false,
])

@php
    $paddingClass = $noPadding ? '' : $padding;
@endphp

<div {{ $attributes->merge(['class' => "bg-white rounded-2xl border border-slate-100 shadow-sm {$paddingClass}"]) }}>
    {{ $slot }}
</div>
