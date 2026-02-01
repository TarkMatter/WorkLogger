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
