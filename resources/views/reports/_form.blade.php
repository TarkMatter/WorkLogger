@php
    /** @var \App\Models\DailyReport|null $report */
    $report = $report ?? null;
    $date = old('report_date', $report?->report_date?->format('Y-m-d') ?? now()->format('Y-m-d'));
@endphp

<div class="space-y-6">
    @if(!$report)
        <div>
            <x-input-label for="report_date" value="日付" />
            <x-text-input id="report_date" name="report_date" type="date" class="mt-1 block w-full"
                value="{{ $date }}" required />
            <x-input-error :messages="$errors->get('report_date')" class="mt-2" />
        </div>
    @else
        <div>
            <div class="text-sm text-gray-500">日付</div>
            <div class="text-lg font-semibold">{{ $report->report_date->format('Y-m-d') }}</div>
        </div>
    @endif

    <div>
        <x-input-label for="memo" value="メモ（任意）" />
        <textarea id="memo" name="memo"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
            rows="6">{{ old('memo', $report?->memo) }}</textarea>
        <x-input-error :messages="$errors->get('memo')" class="mt-2" />
    </div>
</div>