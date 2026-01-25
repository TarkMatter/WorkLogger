<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('admin.users_permissions_title') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 p-3 border rounded-md">{{ session('success') }}</div>
                    @endif

                    <div class="mb-4 p-3 border rounded-md bg-gray-50 text-sm text-gray-600">
                        {{ __('admin.note_admin_hidden') }}
                    </div>

                    @if($users->count() === 0)
                        <p class="text-gray-600">No users.</p>
                    @else
                        @php
                            $projectPerms = [
                                'projects.create' => 'C',
                                'projects.update' => 'U',
                                'projects.delete' => 'D',
                            ];
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left border-b">
                                    <tr>
                                        <th class="py-2 pr-4">ID</th>
                                        <th class="py-2 pr-4">{{ __('admin.name') }}</th>
                                        <th class="py-2 pr-4">{{ __('admin.email') }}</th>
                                        <th class="py-2 pr-4">{{ __('admin.role') }}</th>
                                        <th class="py-2 pr-4">Projects</th>
                                        <th class="py-2 pr-4 text-right">{{ __('admin.edit_permissions_title') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $u)
                                    @php $keys = $u->permissions->pluck('key')->all(); @endphp
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">{{ $u->id }}</td>
                                        <td class="py-2 pr-4">{{ $u->name }}</td>
                                        <td class="py-2 pr-4">{{ $u->email }}</td>
                                        <td class="py-2 pr-4">{{ $u->role }}</td>

                                        <td class="py-2 pr-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($projectPerms as $key => $label)
                                                    @php $has = in_array($key, $keys, true); @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border
                                                        {{ $has ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-400 border-gray-200' }}">
                                                        {{ $label }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>

                                        <td class="py-2 pr-4 text-right">
                                            <a class="text-blue-700 hover:underline"
                                               href="{{ route('admin.users.permissions.edit', $u) }}">
                                                {{ __('admin.edit_permissions_title') }}
                                            </a>
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
