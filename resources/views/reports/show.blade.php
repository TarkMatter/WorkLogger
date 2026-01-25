<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('reports.show_title_with_date', ['date' => $report->report_date->format('Y-m-d')]) }}
            </h2>

            @can('update', $report)
                <a href="{{ route('reports.edit', $report) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                    {{ __('common.edit') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 space-y-4">
                    @if (session('success'))
                        <div class="mb-4 p-3 border rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-3 border rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 border rounded-md">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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

                    <div class="mb-4 p-4 border rounded-lg {{ $statusClass }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-sm opacity-80">{{ __('reports.labels.status') }}</div>
                                <div class="text-lg font-semibold">{{ $statusLabel }}</div>
                            </div>

                            <div class="text-sm opacity-80 text-right">
                                @if($report->submitted_at)
                                    <div>{{ __('reports.labels.submitted_at') }}: {{ $report->submitted_at->format('Y-m-d H:i') }}</div>
                                @endif
                                @if($report->approved_at)
                                    <div>{{ __('reports.labels.checked_at') }}: {{ $report->approved_at->format('Y-m-d H:i') }}</div>
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
                        <div class="mb-4 p-4 border border-red-200 bg-red-50 rounded-lg">
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

                    <div class="mb-4 p-4 border rounded-lg">
                        <div class="text-sm text-gray-500">{{ __('reports.labels.history') }}</div>

                        @php
                            $statusLabel2 = fn($s) => $s ? __('reports.status.' . $s) : '-';
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
                                                        {{ $statusLabel2($log->from_status) }}
                                                        <span class="mx-1">→</span>
                                                        {{ $statusLabel2($log->to_status) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                {{ $log->created_at->format('Y-m-d H:i') }}
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

                    {{-- approval panel --}}
                    @can('approve', $report)
                        @if($report->status === 'submitted')
                            @php
                                $total = (int) $report->timeEntries->sum('minutes');
                                $hours = intdiv($total, 60);
                                $mins  = $total % 60;

                                $warnZero = ($total === 0);
                                $warnOver = ($total > 24 * 60);

                                $approveDisabled = ($warnZero || $warnOver);

                                $approveDisabledReason = $warnZero
                                    ? __('reports.warnings.reason_zero')
                                    : ($warnOver ? __('reports.warnings.reason_over') : null);

                                $openReject = old('rejection_reason') || $errors->has('rejection_reason') || $warnZero || $warnOver;

                                $initialReason = (string) old('rejection_reason', '');
                                $rejectDisabled = trim($initialReason) === '';
                                $initialCounter = mb_strlen($initialReason);
                            @endphp

                            <div id="approval-panel" class="mt-4 p-4 border rounded-lg space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500">{{ __('reports.labels.approval_actions') }}</div>
                                        <div class="font-semibold">{{ __('reports.labels.submitted_notice') }}</div>

                                        <div class="mt-1 text-sm text-gray-600">
                                            {{ __('reports.labels.total') }}: <span class="font-semibold">{{ $hours }}h {{ $mins }}m</span>
                                            <span class="text-gray-500">({{ $total }}{{ __('reports.units.minutes') }})</span>
                                        </div>

                                        @if($warnZero || $warnOver)
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @if($warnZero)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-red-50 text-red-800 border-red-200">
                                                        {{ __('reports.warnings.badge_zero') }}
                                                    </span>
                                                @endif
                                                @if($warnOver)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-amber-50 text-amber-900 border-amber-200">
                                                        {{ __('reports.warnings.badge_over') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        @if($approveDisabledReason)
                                            <div class="mt-2 text-sm text-red-700">
                                                {{ $approveDisabledReason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    {{-- approve --}}
                                    <form method="POST" action="{{ route('reports.approve', ['dailyReport' => $report]) }}"
                                          @if(!$approveDisabled)
                                          onsubmit="return confirm({{ json_encode(__('reports.confirm.approve')) }});"
                                          @endif
                                    >
                                        @csrf
                                        <x-primary-button @disabled($approveDisabled) title="{{ $approveDisabledReason ?? '' }}">
                                            {{ __('reports.buttons.approve') }}
                                        </x-primary-button>
                                    </form>

                                    {{-- reject toggle --}}
                                    <button type="button"
                                            class="inline-flex items-center px-4 py-2 border rounded-md bg-white hover:bg-gray-50"
                                            onclick="document.getElementById('reject-panel').classList.toggle('hidden');">
                                        {{ __('reports.buttons.reject') }}
                                    </button>
                                </div>

                                {{-- reject panel --}}
                                <div id="reject-panel" class="{{ $openReject ? '' : 'hidden' }} mt-3 p-4 border rounded-lg bg-gray-50">
                                    <form method="POST" action="{{ route('reports.reject', ['dailyReport' => $report]) }}"
                                          class="space-y-3"
                                          onsubmit="return confirm({{ json_encode(__('reports.confirm.reject')) }});">
                                        @csrf

                                        <div>
                                            <x-input-label for="rejection_reason" :value="__('reports.rejection.required')" />
                                            <textarea id="rejection_reason" name="rejection_reason" required
                                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                                      rows="4"
                                                      placeholder="{{ __('reports.rejection.placeholder') }}"
                                                      oninput="document.getElementById('reject-counter').textContent = this.value.length; document.getElementById('reject-submit').disabled = (this.value.trim().length === 0);"
                                            >{{ old('rejection_reason') }}</textarea>
                                            <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />

                                            <div class="mt-1 text-xs text-gray-500">
                                                <span id="reject-counter">{{ $initialCounter }}</span>{{ __('reports.rejection.counter_suffix') }}
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <x-danger-button id="reject-submit" @disabled($rejectDisabled)>{{ __('reports.buttons.reject') }}</x-danger-button>
                                            <button type="button" class="text-sm text-gray-600 hover:underline"
                                                    onclick="document.getElementById('reject-panel').classList.add('hidden');">
                                                {{ __('common.cancel') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endcan

                    {{-- time entries summary (read-only) --}}
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

                        @if($report->timeEntries->count() === 0)
                            <p class="mt-3 text-gray-700">{{ __('reports.empty.entries') }}</p>
                        @else
                            <div class="mt-3 overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-left border-b">
                                        <tr>
                                            <th class="py-2 pr-4">{{ __('reports.labels.project') }}</th>
                                            <th class="py-2 pr-4">{{ __('reports.labels.task_optional') }}</th>
                                            <th class="py-2 pr-4">{{ __('reports.labels.minutes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($report->timeEntries as $entry)
                                        <tr class="border-b">
                                            <td class="py-2 pr-4">{{ $entry->project->name }}</td>
                                            <td class="py-2 pr-4">{{ $entry->task ?? '-' }}</td>
                                            <td class="py-2 pr-4">{{ $entry->minutes }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- delete --}}
                    @can('delete', $report)
                        <form method="POST" action="{{ route('reports.destroy', $report) }}" class="mt-2"
                              onsubmit="return confirm({{ json_encode(__('reports.confirm.delete_report')) }});">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>{{ __('common.delete') }}</x-danger-button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
