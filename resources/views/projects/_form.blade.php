@php
    /** @var \App\Models\Project|null $project */
    $project = $project ?? null;

    $starts = old('starts_on', $project?->starts_on?->format('Y-m-d'));
    $ends   = old('ends_on',   $project?->ends_on?->format('Y-m-d'));
@endphp

<div class="space-y-6">
    <div>
        <x-input-label for="name" value="案件名" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
            value="{{ old('name', $project?->name) }}" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="code" value="案件コード（任意）" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
            value="{{ old('code', $project?->code) }}" />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="ステータス" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            @php $status = old('status', $project?->status ?? 'active'); @endphp
            <option value="active"   @selected($status === 'active')>稼働中</option>
            <option value="archived" @selected($status === 'archived')>アーカイブ</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="starts_on" value="開始日（任意）" />
            <x-text-input id="starts_on" name="starts_on" type="date" class="mt-1 block w-full"
                value="{{ $starts }}" />
            <x-input-error :messages="$errors->get('starts_on')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="ends_on" value="終了日（任意）" />
            <x-text-input id="ends_on" name="ends_on" type="date" class="mt-1 block w-full"
                value="{{ $ends }}" />
            <x-input-error :messages="$errors->get('ends_on')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="description" value="説明（任意）" />
        <textarea id="description" name="description"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
            rows="4">{{ old('description', $project?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
</div>