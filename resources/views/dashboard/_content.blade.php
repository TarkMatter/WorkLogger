@php
    $pendingApprovals = $pendingApprovals ?? collect();
    $pendingApprovalsCount = $pendingApprovalsCount ?? $pendingApprovals->count();
    $rejectedReports = $rejectedReports ?? collect();
    $rejectedReportsCount = $rejectedReportsCount ?? $rejectedReports->count();
@endphp

<div class="space-y-8">
    @if(auth()->user()->canApprove())
        @php
            $hasPending = $pendingApprovalsCount > 0;
        @endphp
        <section class="p-4 border rounded-lg bg-white">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-semibold">{{ __('dashboard.pending_approvals_title') }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-gray-100 text-gray-700">
                            {{ $pendingApprovalsCount }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500">
                        {{ $hasPending ? __('dashboard.pending_approvals_desc_has') : __('dashboard.pending_approvals_desc_none') }}
                    </p>
                </div>

                <a href="{{ route('reports.index', ['status' => 'submitted']) }}"
                   class="text-sm text-gray-600 hover:underline">
                    {{ __('dashboard.pending_approvals_view_all') }}
                </a>
            </div>

            @if($hasPending)
                <div class="mt-6 space-y-6">
                    @foreach($pendingApprovals as $report)
                        @php
                            $total = (int) ($report->total_minutes ?? 0);
                            $h = intdiv($total, 60);
                            $m = $total % 60;
                        @endphp
                        <div class="flex items-center justify-between gap-4 p-4 border rounded-md">
                            <div>
                                <div class="font-semibold">
                                    <x-datetime :value="$report->report_date" type="date" />
                                    <span class="text-sm text-gray-500">・{{ $report->user->name }}</span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ __('reports.labels.total') }}: {{ $h }}h {{ $m }}m ({{ $total }}{{ __('reports.units.minutes') }})
                                </div>
                            </div>

                            <a href="{{ route('reports.show', $report) }}#approval-panel"
                               class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50">
                                {{ __('reports.buttons.process') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @php
        $hasRejected = $rejectedReportsCount > 0;
    @endphp
    <section class="p-4 border rounded-lg bg-white">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-semibold">{{ __('dashboard.rejected_reports_title') }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-gray-100 text-gray-700">
                        {{ $rejectedReportsCount }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">
                    {{ $hasRejected ? __('dashboard.rejected_reports_desc_has') : __('dashboard.rejected_reports_desc_none') }}
                </p>
            </div>

            <a href="{{ route('reports.index', ['status' => 'rejected']) }}"
               class="text-sm text-gray-600 hover:underline">
                {{ __('dashboard.rejected_reports_view_all') }}
            </a>
        </div>

        @if($hasRejected)
            <div class="mt-6 space-y-6">
                @foreach($rejectedReports as $report)
                    @php
                        $total = (int) ($report->total_minutes ?? 0);
                        $h = intdiv($total, 60);
                        $m = $total % 60;
                    @endphp
                    <div class="flex items-center justify-between gap-4 p-4 border rounded-md">
                        <div>
                            <div class="font-semibold">
                                <x-datetime :value="$report->report_date" type="date" />
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ __('reports.labels.total') }}: {{ $h }}h {{ $m }}m ({{ $total }}{{ __('reports.units.minutes') }})
                            </div>
                        </div>

                        @can('update', $report)
                            <a href="{{ route('reports.edit', $report) }}"
                               class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50">
                                {{ __('reports.buttons.edit') }}
                            </a>
                        @else
                            <a href="{{ route('reports.show', $report) }}"
                               class="text-gray-700 hover:underline">
                                {{ __('reports.buttons.detail') }}
                            </a>
                        @endcan
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
