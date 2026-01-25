<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('projects.title') }}</h2>

            @can('create', \App\Models\Project::class)
                <a href="{{ route('projects.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                    {{ __('common.create') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if (session('success'))
                        <div class="mb-4 p-3 border rounded-md">{{ session('success') }}</div>
                    @endif

                    @if($projects->count() === 0)
                        <p>{{ __('projects.empty') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left border-b">
                                    <tr>
                                        <th class="py-2 pr-4">{{ __('projects.labels.name') }}</th>
                                        <th class="py-2 pr-4">{{ __('projects.labels.description') }}</th>
                                        <th class="py-2 pr-4 text-right">{{ __('common.detail') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4 font-semibold">
                                            <a href="{{ route('projects.show', $project) }}" class="text-blue-700 hover:underline">
                                                {{ $project->name }}
                                            </a>
                                        </td>
                                        <td class="py-2 pr-4 text-gray-700">{{ $project->description }}</td>
                                        <td class="py-2 pr-4 text-right">
                                            <div class="inline-flex items-center gap-2">
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

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
