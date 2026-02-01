@php
    $project = $project ?? null;

    $code = old('code', $project?->code);
    $name = old('name', $project?->name);
    $description = old('description', $project?->description);

    // date input value は ISO 形式 (Y-m-d) が必須
    $startDate = old('start_date', $project?->start_date?->format('Y-m-d'));
    $endDate   = old('end_date', $project?->end_date?->format('Y-m-d'));
@endphp

<div class="space-y-6">
    {{-- code --}}
    <div>
        <label class="text-sm font-semibold text-gray-700" for="code">
            {{ __('projects.labels.code') }}
        </label>
        <input id="code" name="code" type="text"
               value="{{ $code }}"
               required
               maxlength="50"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        @error('code')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    {{-- name --}}
    <div>
        <label class="text-sm font-semibold text-gray-700" for="name">
            {{ __('projects.labels.name') }}
        </label>
        <input id="name" name="name" type="text"
               value="{{ $name }}"
               required
               maxlength="255"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        @error('name')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    {{-- dates --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <label class="text-sm font-semibold text-gray-700" for="start_date">
                {{ __('projects.labels.start_date') }}
            </label>
            <input id="start_date" name="start_date" type="date"
                   value="{{ $startDate }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            @error('start_date')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="text-sm font-semibold text-gray-700" for="end_date">
                {{ __('projects.labels.end_date') }}
            </label>
            <input id="end_date" name="end_date" type="date"
                   value="{{ $endDate }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            @error('end_date')
                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- description --}}
    <div>
        <label class="text-sm font-semibold text-gray-700" for="description">
            {{ __('projects.labels.description') }}
        </label>
        <textarea id="description" name="description" rows="4"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ $description }}</textarea>
        @error('description')
            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>
