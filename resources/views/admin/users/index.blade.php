<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.users_permissions_title') }}</h2>
            <a href="{{ route('register') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                {{ __('admin.create_user') }}
            </a>
        </div>
    </x-slot>

    <x-page-card maxWidth="6xl" bodyClass="p-6 space-y-6">
                    <div class="p-3 border rounded-md bg-gray-50 text-sm text-gray-600">
                        {{ __('admin.note_admin_hidden') }}
                    </div>

                    @include('admin.users._table', ['users' => $users])

    </x-page-card>
</x-app-layout>
