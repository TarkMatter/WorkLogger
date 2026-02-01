<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('projects.detail_title') }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:underline">
                ← {{ __('common.back') }}
            </a>
        </div>
    </x-slot>

    <x-page-card maxWidth="3xl" bodyClass="p-6 space-y-6">
                    @include('projects._show_details', ['project' => $project])

                    <div class="pt-4 flex justify-end gap-3">
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
    </x-page-card>
</x-app-layout>
