@php
    $statusClassMap = [
        'draft'     => 'bg-gray-50 text-gray-900 border-gray-200',
        'submitted' => 'bg-amber-50 text-amber-900 border-amber-200',
        'approved'  => 'bg-emerald-50 text-emerald-900 border-emerald-200',
        'rejected'  => 'bg-red-50 text-red-900 border-red-200',
    ];

    $statusLabel = __('reports.status.' . $report->status);
    $statusClass = $statusClassMap[$report->status] ?? 'bg-gray-50 text-gray-900 border-gray-200';
@endphp

<div class="p-4 border rounded-lg {{ $statusClass }}">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-sm opacity-80">{{ __('reports.labels.status') }}</div>
            <div class="text-lg font-semibold">{{ $statusLabel }}</div>
        </div>

        <div class="text-sm opacity-80 text-right">
            @if($report->submitted_at)
                <div>{{ __('reports.labels.submitted_at') }}: <x-datetime :value="$report->submitted_at" /></div>
            @endif
            @if($report->approved_at)
                <div>{{ __('reports.labels.checked_at') }}: <x-datetime :value="$report->approved_at" /></div>
            @endif
        </div>
    </div>

    @if(in_array($report->status, ['approved','rejected'], true) && $report->approver)
        <div class="mt-2 text-sm opacity-80">
            {{ $report->status === 'approved' ? __('reports.labels.approver') : __('reports.labels.rejector') }}: {{ $report->approver->name }}
        </div>
    @endif
</div>

@if($report->status === 'rejected' && $report->rejection_reason)
    <div class="p-4 border border-red-200 bg-red-50 rounded-lg">
        <div class="text-sm font-semibold text-red-700">{{ __('reports.rejection.latest') }}</div>
        <div class="mt-2 whitespace-pre-wrap text-red-900">{{ $report->rejection_reason }}</div>

        @can('update', $report)
            <div class="mt-3">
                <a href="{{ route('reports.edit', $report) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                    {{ __('reports.buttons.fix_and_resubmit') }}
                </a>
            </div>
        @endcan
    </div>
@endif
