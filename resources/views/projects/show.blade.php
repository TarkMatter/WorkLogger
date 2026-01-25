<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('projects.detail_title') }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:underline">
                ← {{ __('common.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-sm text-gray-500">{{ __('projects.labels.name') }}</div>
                        <div class="text-lg font-semibold">{{ $project->name }}</div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">{{ __('projects.labels.description') }}</div>
                        <div class="mt-1 text-gray-800 whitespace-pre-wrap">
                            {{ $project->description ?: __('projects.unset') }}
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end gap-2">
                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project) }}"
                               class="inline-flex items-center px-4 py-2 border rounded-md bg-white hover:bg-gray-50">
                                {{ __('common.edit') }}
                            </a>
                        @endcan
                        <a href="{{ route('projects.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            {{ __('common.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
