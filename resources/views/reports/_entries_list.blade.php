@php
    $showDelete = $showDelete ?? false;
    $showEmptyMessage = $showEmptyMessage ?? true;
    $containerClass = $containerClass ?? 'overflow-x-auto';
    $emptyClass = $emptyClass ?? 'text-gray-700';
@endphp

@if($report->timeEntries->count() === 0)
    @if($showEmptyMessage)
        <p class="{{ $emptyClass }}">{{ __('reports.empty.entries') }}</p>
    @endif
@else
    <div class="{{ $containerClass }}">
        <table class="min-w-full text-sm">
            <thead class="text-left border-b">
                <tr>
                    <th class="py-3 pr-10">{{ __('reports.labels.project') }}</th>
                    <th class="py-3 pr-10">{{ __('reports.labels.task_optional') }}</th>
                    <th class="py-3 pr-10">{{ __('reports.labels.minutes') }}</th>
                    @if($showDelete)
                        <th class="py-3 pr-10"></th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @foreach($report->timeEntries as $entry)
                <tr class="border-b">
                    <td class="py-4 pr-10">{{ $entry->project->name }}</td>
                    <td class="py-4 pr-10">{{ $entry->task ?? '-' }}</td>
                    <td class="py-4 pr-10">{{ $entry->minutes }}</td>
                    @if($showDelete)
                        <td class="py-4 pr-10 text-right">
                            <form method="POST"
                                  action="{{ route('reports.entries.destroy', ['report' => $report, 'entry' => $entry]) }}"
                                  data-confirm="{{ __('confirm.delete', ['item' => __('models.time_entry')]) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-700 hover:underline">{{ __('common.delete') }}</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
