@php
    $cancelHref = $cancelHref ?? route('projects.index');
    $submitLabel = $submitLabel ?? __('common.save');
@endphp

<div class="flex justify-end gap-3">
    <a href="{{ $cancelHref }}"
       class="inline-flex items-center px-4 py-2 border rounded-md bg-white hover:bg-gray-50">
        {{ __('common.cancel') }}
    </a>
    <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
        {{ $submitLabel }}
    </button>
</div>
