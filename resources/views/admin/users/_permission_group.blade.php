@php
    $groupId = 'grp_' . preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $group);

    $groupKey = 'permissions.group.' . $group;
    $groupLabel = \Illuminate\Support\Facades\Lang::has($groupKey)
        ? __($groupKey)
        : $group;
@endphp

<div class="p-4 border rounded-md">
    <div class="flex items-center justify-between gap-3">
        <div class="font-semibold">{{ $groupLabel }}</div>

        <div class="flex items-center gap-3">
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

    <div id="{{ $groupId }}" class="mt-4 space-y-3">
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

            <label class="flex items-start gap-4">
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
