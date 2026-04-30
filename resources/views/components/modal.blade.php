@props([
    'id'    => 'modal',
    'title' => 'Konfirmasi',
    'size'  => 'md',   {{-- sm | md | lg | xl --}}
])

@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

{{-- Modal Backdrop + Container --}}
<div
    id="{{ $id }}"
    class="fixed inset-0 z-[100] hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}-title"
>
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
        onclick="document.getElementById('{{ $id }}').classList.add('hidden')"
    ></div>

    {{-- Dialog Panel --}}
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white w-full {{ $sizeClass }} rounded-3xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-start justify-between px-6 pt-6 pb-4 border-b border-slate-100">
                <h3 id="{{ $id }}-title" class="text-base font-bold text-slate-800">
                    {{ $title }}
                </h3>
                <button
                    onclick="document.getElementById('{{ $id }}').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-500 flex items-center justify-center text-slate-400 transition-colors -mt-0.5 -mr-0.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 text-sm text-slate-500 leading-relaxed">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div class="flex items-center gap-3 px-6 pb-6">
                    {{ $footer }}
                </div>
            @endisset

        </div>
    </div>
</div>
