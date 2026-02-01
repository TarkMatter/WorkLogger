@if($projects->count() === 0)
    <p>{{ __('projects.empty') }}</p>
@else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border-separate border-spacing-x-8 border-spacing-y-2">
            <thead class="text-left border-b">
                <tr>
                    <th class="py-3 px-4">{{ __('projects.labels.code') }}</th>
                    <th class="py-3 px-4">{{ __('projects.labels.name') }}</th>
                    <th class="py-3 px-4">{{ __('projects.labels.start_date') }}</th>
                    <th class="py-3 px-4">{{ __('projects.labels.end_date') }}</th>
                    <th class="py-3 px-4">{{ __('projects.labels.description') }}</th>
                    <th class="py-3 px-4 text-right">{{ __('common.detail') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($projects as $project)
                <tr class="border-b">
                    <td class="py-4 px-4 text-gray-700">{{ $project->code }}</td>
                    <td class="py-4 px-4 font-semibold">
                        <a href="{{ route('projects.show', $project) }}" class="text-blue-700 hover:underline">
                            {{ $project->name }}
                        </a>
                    </td>
                    <td class="py-4 px-4 text-gray-700">
                        {{ $project->start_date?->format('Y-m-d') ?? __('projects.unset') }}
                    </td>
                    <td class="py-4 px-4 text-gray-700">
                        {{ $project->end_date?->format('Y-m-d') ?? __('projects.unset') }}
                    </td>
                    <td class="py-4 px-4 text-gray-700">
                        {{ $project->description ?: __('projects.unset') }}
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="inline-flex items-center gap-3">
                            <a href="{{ route('projects.show', $project) }}"
                               class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50">
                                {{ __('common.detail') }}
                            </a>

                            @can('update', $project)
                                <a href="{{ route('projects.edit', $project) }}"
                                   class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50">
                                    {{ __('common.edit') }}
                                </a>
                            @endcan

                            @can('delete', $project)
                                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                      onsubmit="return confirm({{ json_encode(__('projects.confirm.delete')) }});">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50">
                                        {{ __('common.delete') }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
