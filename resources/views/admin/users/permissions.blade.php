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

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if (session('success'))
                        <div class="mb-4 p-3 border rounded-md">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 border rounded-md">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="p-3 border rounded-md bg-gray-50 text-sm text-gray-600">
                            {{ __('admin.bulk_hint') }}
                        </div>

                        {{-- user info --}}
                        <div class="p-4 border rounded-md">
                            <div class="font-semibold mb-3">{{ __('admin.user_info') }}</div>

                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="text-sm font-semibold text-gray-700">{{ __('admin.name') }}</label>
                                    <input id="name" name="name" type="text"
                                           value="{{ old('name', $user->name) }}"
                                           required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                    @error('name')
                                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="text-sm font-semibold text-gray-700">{{ __('admin.email') }}</label>
                                    <input id="email" name="email" type="email"
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                    @error('email')
                                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="role" class="text-sm font-semibold text-gray-700">{{ __('admin.role') }}</label>
                                    <select id="role" name="role"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="member" @selected(old('role', $user->role) === 'member')>member</option>
                                        <option value="approver" @selected(old('role', $user->role) === 'approver')>approver</option>
                                        <option value="admin" @selected(old('role', $user->role) === 'admin')>admin</option>
                                    </select>
                                    @error('role')
                                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- permissions --}}
                        @foreach($permissions as $group => $items)
                            @php
                                $groupId = 'grp_' . preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$group);

                                $groupKey = 'permissions.group.' . $group;
                                $groupLabel = \Illuminate\Support\Facades\Lang::has($groupKey)
                                    ? __($groupKey)
                                    : $group;
                            @endphp

                            <div class="p-4 border rounded-md">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="font-semibold">{{ $groupLabel }}</div>

                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                class="px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm"
                                                onclick="toggleGroup('{{ $groupId }}', true)">
                                            {{ __('admin.all_on') }}
                                        </button>
                                        <button type="button"
                                                class="px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm"
                                                onclick="toggleGroup('{{ $groupId }}', false)">
                                            {{ __('admin.all_off') }}
                                        </button>
                                    </div>
                                </div>

                                <div id="{{ $groupId }}" class="mt-3 space-y-2">
                                    @foreach($items as $p)
                                        @php
                                            $labelKey = 'permissions.label.' . $p->key;
                                            $descKey  = 'permissions.description.' . $p->key;

                                            $label = \Illuminate\Support\Facades\Lang::has($labelKey)
                                                ? __($labelKey)
                                                : ($p->label ?? $p->key);

                                            $desc = \Illuminate\Support\Facades\Lang::has($descKey)
                                                ? __($descKey)
                                                : ($p->description ?? null);
                                        @endphp

                                        <label class="flex items-start gap-3">
                                            <input type="checkbox"
                                                   class="perm-checkbox"
                                                   name="permissions[]"
                                                   value="{{ $p->id }}"
                                                   @checked(in_array($p->id, $assigned, true)) />

                                            <div>
                                                <div class="text-sm font-semibold">{{ $label }}</div>
                                                <div class="text-xs text-gray-500">{{ $p->key }}</div>
                                                @if($desc)
                                                    <div class="text-xs text-gray-500">{{ $desc }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="flex justify-end">
                            <x-primary-button>{{ __('common.save') }}</x-primary-button>
                        </div>
                    </form>

                    <script>
                        function toggleGroup(groupId, on) {
                            const root = document.getElementById(groupId);
                            if (!root) return;

                            root.querySelectorAll('input.perm-checkbox').forEach(cb => {
                                cb.checked = !!on;
                            });
                        }
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
