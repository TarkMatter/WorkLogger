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

    <x-page-card maxWidth="7xl" bodyClass="p-6 space-y-6">

                    @include('projects._table', ['projects' => $projects])

    </x-page-card>
</x-app-layout>
