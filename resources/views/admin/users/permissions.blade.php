<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('admin.edit_permissions_title') }}：{{ $user->name }}（{{ $user->email }}）
            </h2>

            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">
                ← {{ __('common.back') }}
            </a>
        </div>
    </x-slot>

    <x-page-card maxWidth="3xl" bodyClass="p-6 space-y-6">

                    @include('admin.users._errors')

                    <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        @include('admin.users._bulk_hint')

                        @include('admin.users._user_info', ['user' => $user])

                        @foreach($permissions as $group => $items)
                            @include('admin.users._permission_group', [
                                'group' => $group,
                                'items' => $items,
                                'assigned' => $assigned,
                            ])
                        @endforeach

                        @include('admin.users._form_actions')
                    </form>

                    @include('admin.users._toggle_script')

    </x-page-card>
</x-app-layout>
