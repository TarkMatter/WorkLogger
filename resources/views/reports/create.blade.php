<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('reports.create_title') }}</h2>
    </x-slot>

    <x-page-card maxWidth="3xl" bodyClass="p-6 space-y-6" cardClass="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form method="POST" action="{{ route('reports.store') }}" class="space-y-6">
                        @csrf
                        @include('reports._form')

                        @include('reports._create_actions')
                    </form>
    </x-page-card>
</x-app-layout>
