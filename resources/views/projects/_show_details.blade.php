<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div>
        <div class="text-sm text-gray-500">{{ __('projects.labels.code') }}</div>
        <div class="text-lg font-semibold">{{ $project->code }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">{{ __('projects.labels.name') }}</div>
        <div class="text-lg font-semibold">{{ $project->name }}</div>
    </div>

    <div>
        <div class="text-sm text-gray-500">{{ __('projects.labels.start_date') }}</div>
        <div class="text-gray-800">
            {{ $project->start_date?->format('Y-m-d') ?? __('projects.unset') }}
        </div>
    </div>

    <div>
        <div class="text-sm text-gray-500">{{ __('projects.labels.end_date') }}</div>
        <div class="text-gray-800">
            {{ $project->end_date?->format('Y-m-d') ?? __('projects.unset') }}
        </div>
    </div>
</div>

<div>
    <div class="text-sm text-gray-500">{{ __('projects.labels.description') }}</div>
    <div class="mt-1 text-gray-800 whitespace-pre-wrap">
        {{ $project->description ?: __('projects.unset') }}
    </div>
</div>
