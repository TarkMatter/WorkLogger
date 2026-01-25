<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('reports.create_title') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('reports.store') }}">
                        @csrf
                        @include('reports._form')

                        <div class="mt-6 flex gap-3">
                            <x-primary-button>{{ __('reports.buttons.create_and_edit') }}</x-primary-button>
                            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md">
                                {{ __('common.back') }}
                            </a>
                        </div>

                        <p class="mt-4 text-sm text-gray-500">
                            {{ __('reports.notes.create_redirect') }}
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
