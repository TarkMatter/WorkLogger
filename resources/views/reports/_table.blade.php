@if($reports->count() === 0)
    <p>{{ __('reports.empty.reports') }}</p>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border-separate border-spacing-x-8 border-spacing-y-2">
            <thead class="text-left border-b">
                <tr>
                    <th class="py-3 px-4">{{ __('reports.labels.date') }}</th>
                    <th class="py-3 px-4">{{ __('reports.labels.status') }}</th>
                    <th class="py-3 px-4">{{ __('reports.labels.total') }}</th>
                    @if(auth()->user()->canApprove())
                        <th class="py-3 px-4">{{ __('reports.labels.user') }}</th>
                    @endif
                    <th class="py-3 px-4 text-right">{{ __('reports.labels.operations') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($reports as $report)
                @php
                    $label = __('reports.status.' . $report->status);

                    $total = (int) ($report->total_minutes ?? 0);
                    $h = intdiv($total, 60);
                    $m = $total % 60;

                    $warnZero = ($total === 0);
                    $warnOver = ($total > 24 * 60);

                    $isApprover = auth()->user()->canApprove();

                    $canReview = auth()->user()->can('approve', $report);

                    $rowHref = $canReview
                        ? route('reports.show', $report) . '#approval-panel'
                        : route('reports.show', $report);

                    $rowHover = 'hover:bg-amber-50';
                    if ($warnZero) $rowHover = 'hover:bg-red-50';
                    if ($warnOver) $rowHover = 'hover:bg-amber-100';
                @endphp

                <tr class="border-b {{ $canReview ? $rowHover . ' cursor-pointer' : '' }}"
                    @if($canReview)
                        role="link"
                        tabindex="0"
                        onclick="window.location.href={{ json_encode($rowHref) }};"
                        onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location.href={{ json_encode($rowHref) }}; }"
                    @endif
                >
                    <td class="py-4 px-4">
                        <a class="text-blue-700 hover:underline"
                           href="{{ $rowHref }}"
                           onclick="event.stopPropagation();">
                            <x-datetime :value="$report->report_date" type="date" />
                        </a>
                    </td>

                    <td class="py-4 px-4">
                        {{ $label }}

                        @if($isApprover && $report->status === 'submitted' && ($warnZero || $warnOver))
                            <div class="mt-1 flex flex-wrap gap-2">
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
                    </td>

                    <td class="py-4 px-4">
                        {{ $h }}h {{ $m }}m
                        <span class="text-gray-500">({{ $total }}{{ __('reports.units.minutes') }})</span>
                    </td>

                    @if($isApprover)
                        <td class="py-4 px-4">
                            {{ $report->user->name }}
                            @if((int)$report->user_id === (int)auth()->id())
                                <span class="ml-2 text-xs text-gray-500">{{ __('reports.misc.self') }}</span>
                            @endif
                        </td>
                    @endif

                    <td class="py-4 px-4 text-right">
                        @if($isApprover)
                            @if($canReview)
                                <a href="{{ $rowHref }}"
                                   onclick="event.stopPropagation();"
                                   class="inline-flex items-center px-3 py-1.5 border rounded-md bg-gray-800 text-white hover:bg-gray-700">
                                    {{ __('reports.buttons.process') }}
                                </a>
                            @else
                                <a href="{{ route('reports.show', $report) }}"
                                   onclick="event.stopPropagation();"
                                   class="text-gray-700 hover:underline">
                                    {{ __('reports.buttons.detail') }}
                                </a>
                            @endif
                        @else
                            @can('update', $report)
                                <a class="text-gray-700 hover:underline"
                                   href="{{ route('reports.edit', $report) }}"
                                   onclick="event.stopPropagation();">
                                    {{ __('reports.buttons.edit') }}
                                </a>
                            @else
                                <a class="text-gray-700 hover:underline"
                                   href="{{ route('reports.show', $report) }}"
                                   onclick="event.stopPropagation();">
                                    {{ __('reports.buttons.detail') }}
                                </a>
                            @endcan
                        @endif
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reports->links() }}
    </div>
@endif
