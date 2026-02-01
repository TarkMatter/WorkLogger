@php
    $count = fn($key) => (int) ($counts[$key] ?? 0);
@endphp

<div class="flex flex-wrap gap-3">
    @foreach($tabs as $key)
        @php
            $isActive = $current === $key;

            // submitted以外ではwarnをリセット
            $href = route('reports.index', [
                'status' => $key,
                'sort' => $sort,
                'dir' => $dir,
                'warn' => ($key === 'submitted') ? $warn : 'all',
            ]);

            $badge = $key === 'all' ? (int) $totalCount : $count($key);
        @endphp

        <a href="{{ $href }}"
           class="inline-flex items-center gap-2 px-3 py-1.5 border rounded-full text-sm
                  {{ $isActive ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
            {{ $statusLabels[$key] ?? $key }}

            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                         {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                {{ $badge }}
            </span>
        </a>
    @endforeach
</div>
