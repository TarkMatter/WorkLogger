@if($report->status === 'rejected' && $report->rejection_reason)
    <div class="p-4 border border-red-200 bg-red-50 rounded-lg">
        <div class="text-sm font-semibold text-red-700">{{ __('reports.rejection.latest') }}</div>
        <div class="mt-2 whitespace-pre-wrap text-red-900">{{ $report->rejection_reason }}</div>
    </div>
@endif

{{-- update --}}
<form id="report-update-form" method="POST" action="{{ route('reports.update', $report) }}">
    @csrf
    @method('PUT')

    @include('reports._form', ['report' => $report])
</form>

{{-- submit --}}
@can('submit', $report)
    <form id="report-submit-form"
        method="POST"
        action="{{ route('reports.submit', $report) }}"
        data-confirm="{{ __('confirm.submit', ['item' => __('models.daily_report')]) }}">
        @csrf
    </form>
@endcan

<div class="flex items-center gap-4">
    <x-primary-button type="submit" form="report-update-form">{{ __('reports.buttons.update') }}</x-primary-button>

    @can('submit', $report)
        <x-primary-button type="submit" form="report-submit-form">{{ __('reports.buttons.submit') }}</x-primary-button>
    @endcan

    <a href="{{ route('reports.show', $report) }}" class="inline-flex items-center px-4 py-2 border rounded-md">
        {{ __('reports.buttons.go_detail') }}
    </a>
    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md">
        {{ __('reports.buttons.back_to_index') }}
    </a>
</div>
