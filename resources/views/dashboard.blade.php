<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('dashboard.title') }}
        </h2>
    </x-slot>

    <x-page-card maxWidth="7xl" bodyClass="p-6 text-gray-900" cardClass="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        @include('dashboard._content')
    </x-page-card>
</x-app-layout>
