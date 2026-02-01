@props([
    'maxWidth' => '7xl',
    'bodyClass' => 'p-6',
    'cardClass' => 'bg-white shadow-sm sm:rounded-lg',
])

@php
    // よく使う幅は固定マッピングで Tailwind の生成対象に含める。
    $maxWidthClass = match ($maxWidth) {
        '3xl' => 'max-w-3xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        default => 'max-w-7xl',
    };
@endphp

<div class="py-12">
    <div class="mx-auto sm:px-6 lg:px-8 {{ $maxWidthClass }}">
        <div class="{{ $cardClass }}">
            <div {{ $attributes->merge(['class' => $bodyClass]) }}>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
