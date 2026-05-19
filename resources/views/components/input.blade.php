@props([
    'label'       => null,
    'name'        => '',
    'hint'        => null,
    'error'       => null,
    'icon'        => null,   {{-- optional SVG path string --}}
    'trailing'    => null,   {{-- optional trailing slot --}}
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700 mb-1.5">
            {{ $label }}
        </label>
    @endif

    <div class="relative flex items-center">
        @if($icon)
            <div class="absolute left-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
            </div>
        @endif

        <input
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $attributes->merge([
                'class' => implode(' ', array_filter([
                    'w-full py-2.5 bg-slate-50 border rounded-xl text-sm text-slate-800',
                    'placeholder:text-slate-400',
                    'focus:ring-2 focus:ring-emerald-400 focus:border-transparent focus:bg-white',
                    'outline-none transition-all duration-150',
                    $icon    ? 'pl-10 pr-4'  : 'px-4',
                    $error   ? 'border-rose-300 bg-rose-50 focus:ring-rose-300' : 'border-slate-200',
                ]))
            ]) }}
        >

        @if(isset($trailing))
            <div class="absolute right-3.5 flex items-center">
                {{ $trailing }}
            </div>
        @endif
    </div>

    @if($error)
        <p class="mt-1.5 text-xs font-medium text-rose-500">{{ $error }}</p>
    @elseif($hint)
        <p class="mt-1.5 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
