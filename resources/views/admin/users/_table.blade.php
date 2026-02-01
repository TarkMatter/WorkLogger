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
        <table class="min-w-full text-sm border-separate border-spacing-x-8 border-spacing-y-2">
            <thead class="text-left border-b">
                <tr>
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">{{ __('admin.name') }}</th>
                    <th class="py-3 px-4">{{ __('admin.email') }}</th>
                    <th class="py-3 px-4">{{ __('admin.role') }}</th>
                    <th class="py-3 px-4">Projects</th>
                    <th class="py-3 px-4 text-right">{{ __('admin.edit_permissions_title') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
                @php $keys = $u->permissions->pluck('key')->all(); @endphp
                <tr class="border-b">
                    <td class="py-4 px-4">{{ $u->id }}</td>
                    <td class="py-4 px-4">{{ $u->name }}</td>
                    <td class="py-4 px-4">{{ $u->email }}</td>
                    <td class="py-4 px-4">{{ $u->role }}</td>

                    <td class="py-4 px-4">
                        <div class="flex flex-wrap gap-3">
                            @foreach($projectPerms as $key => $label)
                                @php $has = in_array($key, $keys, true); @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border
                                    {{ $has ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-400 border-gray-200' }}">
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    </td>

                    <td class="py-4 px-4 text-right">
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
