<div class="p-4 border rounded-lg">
    <div class="text-sm text-gray-500">{{ __('reports.labels.history') }}</div>

    @php
        $statusLabel = fn($s) => $s ? __('reports.status.' . $s) : '-';
        $actionLabel  = fn($a) => $a ? __('reports.action.' . $a) : '-';
    @endphp

    @if($report->statusLogs->count() === 0)
        <div class="mt-2 text-sm text-gray-500">{{ __('reports.empty.history') }}</div>
    @else
        <div class="mt-3 space-y-3">
            @foreach($report->statusLogs as $log)
                <div class="p-3 border rounded-md">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-sm">
                            <span class="font-semibold">{{ $actionLabel($log->action) }}</span>
                            <span class="text-gray-500">{{ __('common.by') }}</span>
                            <span class="font-semibold">{{ $log->actor->name ?? __('common.unknown') }}</span>

                            @if($log->from_status || $log->to_status)
                                <div class="text-gray-600 mt-1">
                                    {{ $statusLabel($log->from_status) }}
                                    <span class="mx-1">→</span>
                                    {{ $statusLabel($log->to_status) }}
                                </div>
                            @endif
                        </div>

                        <div class="text-xs text-gray-500">
                            <x-datetime :value="$log->created_at" />
                        </div>
                    </div>

                    @if($log->reason)
                        <div class="mt-2 text-sm">
                            <div class="text-xs font-semibold text-gray-500">{{ __('reports.labels.reason') }}</div>
                            <div class="mt-1 whitespace-pre-wrap">{{ $log->reason }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
