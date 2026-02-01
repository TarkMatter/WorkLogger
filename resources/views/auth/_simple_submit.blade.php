@php
    $label = $label ?? __('Submit');
@endphp

<div class="flex items-center justify-end mt-4">
    <x-primary-button>
        {{ $label }}
    </x-primary-button>
</div>
