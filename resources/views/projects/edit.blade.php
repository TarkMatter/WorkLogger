<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('projects.edit_title') }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:underline">
                ← {{ __('common.back') }}
            </a>
        </div>
    </x-slot>

    <x-page-card maxWidth="3xl" bodyClass="p-6 space-y-6">
                    @include('projects._errors')

                    <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        @include('projects._form', ['project' => $project])

                        @include('projects._form_actions', [
                            'cancelHref' => route('projects.show', $project),
                            'submitLabel' => __('common.save'),
                        ])
                    </form>

    </x-page-card>
</x-app-layout>
