@php
    $usePolicy = $usePolicy ?? false;
@endphp

@if(! $usePolicy || auth()->user()->can('delete', $report))
    <form method="POST"
        action="{{ route('reports.destroy', $report) }}"
        data-confirm="{{ __('confirm.delete', ['item' => __('models.daily_report')]) }}">
        @csrf
        @method('DELETE')
        <x-danger-button>{{ __('common.delete') }}</x-danger-button>
    </form>
@endif
