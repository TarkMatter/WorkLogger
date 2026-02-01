@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white', 'menuClass' => '', 'menuStyle' => '', 'menuWidthPx' => null])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '36' => 'w-36',
    '48' => 'w-48',
    '54' => 'w-[13.5rem]',
    '56' => 'w-56',
    '100' => 'w-100',
    default => $width,
};

$menuWidthStyle = '';
if (isset($menuWidthPx) && $menuWidthPx !== '') {
    $menuWidthStyle = 'width: ' . (int) $menuWidthPx . 'px;';
}

$widthClass = $width;
if ($menuWidthStyle !== '') {
    $widthClass = '';
}
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $widthClass }} rounded-md shadow-lg {{ $alignmentClasses }} {{ $menuClass }}"
            style="display: none; {{ $menuWidthStyle }} {{ $menuStyle }}"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
