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

        <div id="approval-panel" class="p-4 border rounded-lg space-y-3">
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
                <form method="POST"
                    action="{{ route('reports.approve', ['report' => $report]) }}"
                    data-confirm="{{ $approveDisabled ? '' : __('confirm.approve', ['item' => __('models.daily_report')]) }}">
                    @csrf
                    <x-primary-button :disabled="$approveDisabled" title="{{ $approveDisabledReason ?? '' }}">
                        {{ __('reports.buttons.approve') }}
                    </x-primary-button>
                </form>

                <button type="button"
                        class="inline-flex items-center px-4 py-2 border rounded-md bg-white hover:bg-gray-50"
                        onclick="document.getElementById('reject-panel').classList.toggle('hidden');">
                    {{ __('reports.buttons.reject') }}
                </button>
            </div>

            <div id="reject-panel" class="{{ $openReject ? '' : 'hidden' }} mt-3 p-4 border rounded-lg bg-gray-50">
                <form method="POST"
                    action="{{ route('reports.reject', ['report' => $report]) }}"
                    class="space-y-2"
                    data-confirm="{{ __('confirm.reject', ['item' => __('models.daily_report')]) }}">
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
                        <x-danger-button id="reject-submit" :disabled="$rejectDisabled">{{ __('reports.buttons.reject') }}</x-danger-button>
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
