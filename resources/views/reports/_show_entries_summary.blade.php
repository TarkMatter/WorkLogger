@php
    $total = (int) $report->timeEntries->sum('minutes');
    $h = intdiv($total, 60);
    $m = $total % 60;
@endphp

<div class="p-4 border rounded-lg">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-sm text-gray-500">{{ __('reports.labels.time_entries') }}</div>
            <div class="text-lg font-semibold">
                {{ __('reports.labels.total') }}: {{ $h }}h {{ $m }}m
                <span class="text-gray-500 text-sm">({{ $total }}{{ __('reports.units.minutes') }})</span>
            </div>
        </div>

        @can('update', $report)
            @if($report->status === 'draft' || $report->status === 'rejected')
                <a href="{{ route('reports.edit', $report) }}"
                   class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50">
                    {{ __('common.edit') }}
                </a>
            @endif
        @endcan
    </div>

    @include('reports._entries_list', [
        'report' => $report,
        'showDelete' => false,
        'showEmptyMessage' => false,
        'containerClass' => 'mt-3 overflow-x-auto',
        'emptyClass' => 'mt-3 text-gray-700',
    ])
</div>
