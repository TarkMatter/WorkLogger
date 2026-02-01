<div class="space-y-6">
    <h3 class="text-lg font-semibold">{{ __('reports.labels.time_entries') }}</h3>

    {{-- add entry --}}
    <form method="POST" action="{{ route('reports.entries.store', $report) }}" class="space-y-5">
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
    @include('reports._entries_list', [
        'report' => $report,
        'showDelete' => true,
        'containerClass' => 'overflow-x-auto',
    ])
</div>
