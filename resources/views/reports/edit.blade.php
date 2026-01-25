<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('reports.edit_title_with_date', ['date' => $report->report_date->format('Y-m-d')]) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($report->status === 'rejected' && $report->rejection_reason)
                        <div class="mb-6 p-4 border border-red-200 bg-red-50 rounded-lg">
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
                        <form id="report-submit-form" method="POST" action="{{ route('reports.submit', $report) }}"
                              onsubmit="return confirm({{ json_encode(__('reports.confirm.submit')) }});">
                            @csrf
                        </form>
                    @endcan

                    <div class="mt-6 flex items-center gap-3">
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

                    <hr class="my-8 mt-3">

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">{{ __('reports.labels.time_entries') }}</h3>

                        {{-- add entry --}}
                        <form method="POST" action="{{ route('reports.entries.store', $report) }}" class="space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="project_id" :value="__('reports.labels.project')" />
                                <select id="project_id" name="project_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">{{ __('reports.time_entries.select_project') }}</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" @selected(old('project_id') == $p->id)>
                                            {{ $p->name }}{{ $p->code ? " ({$p->code})" : "" }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="task" :value="__('reports.labels.task_optional')" />
                                <x-text-input id="task" name="task" type="text" class="mt-1 block w-full"
                                    value="{{ old('task') }}" :placeholder="__('reports.time_entries.task_placeholder')" />
                                <x-input-error :messages="$errors->get('task')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="minutes" :value="__('reports.labels.minutes')" />
                                <x-text-input id="minutes" name="minutes" type="number" class="mt-1 block w-full"
                                    value="{{ old('minutes') }}" min="1" max="{{ 24*60 }}" required />
                                <x-input-error :messages="$errors->get('minutes')" class="mt-2" />
                                <div class="mt-1 text-sm text-gray-500">{{ __('reports.time_entries.minutes_example') }}</div>
                            </div>

                            <x-primary-button>{{ __('reports.buttons.add_entry') }}</x-primary-button>
                        </form>

                        {{-- totals --}}
                        @php
                            $total = $report->timeEntries->sum('minutes');
                            $hours = floor($total / 60);
                            $mins  = $total % 60;
                        @endphp

                        <div class="pt-4">
                            <div class="text-sm text-gray-500">{{ __('reports.labels.total') }}</div>
                            <div class="text-lg font-semibold">{{ $hours }}h {{ $mins }}m({{ $total }}{{ __('reports.units.minutes') }})</div>
                        </div>

                        {{-- list --}}
                        @if($report->timeEntries->count() === 0)
                            <p class="text-gray-700">{{ __('reports.empty.entries') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-left border-b">
                                        <tr>
                                            <th class="py-2 pr-4">{{ __('reports.labels.project') }}</th>
                                            <th class="py-2 pr-4">{{ __('reports.labels.task_optional') }}</th>
                                            <th class="py-2 pr-4">{{ __('reports.labels.minutes') }}</th>
                                            <th class="py-2 pr-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($report->timeEntries as $entry)
                                        <tr class="border-b">
                                            <td class="py-2 pr-4">{{ $entry->project->name }}</td>
                                            <td class="py-2 pr-4">{{ $entry->task ?? '-' }}</td>
                                            <td class="py-2 pr-4">{{ $entry->minutes }}</td>
                                            <td class="py-2 pr-4 text-right">
                                                <form method="POST" action="{{ route('reports.entries.destroy', [$report, $entry]) }}"
                                                    onsubmit="return confirm({{ json_encode(__('reports.confirm.delete_entry')) }});">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-700 hover:underline">{{ __('common.delete') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('reports.destroy', $report) }}" class="mt-6"
                          onsubmit="return confirm({{ json_encode(__('reports.confirm.delete_report')) }});">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>{{ __('common.delete') }}</x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
