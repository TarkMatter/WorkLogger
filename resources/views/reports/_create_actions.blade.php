<div class="flex gap-4">
    <x-primary-button>{{ __('reports.buttons.create_and_edit') }}</x-primary-button>
    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md">
        {{ __('common.back') }}
    </a>
</div>

<p class="text-sm text-gray-500">
    {{ __('reports.notes.create_redirect') }}
</p>
