@if(auth()->user()->canApprove() && $current === 'submitted')
    <div class="space-y-3">
        <div class="text-sm text-gray-500">
            {{ __('reports.notes.approver_submitted_hint') }}
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @php
                $baseParams = [
                    'status' => 'submitted',
                    'sort' => $sort,
                    'dir' => $dir,
                ];

                $allHref = route('reports.index', $baseParams + ['warn' => 'all']);
                $warnHref = route('reports.index', $baseParams + ['warn' => 'warnings']);
            @endphp

            <a href="{{ $allHref }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 border rounded-full text-sm
                      {{ $warn === 'all' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                {{ __('reports.warnings.all') }}
            </a>

            <a href="{{ $warnHref }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 border rounded-full text-sm
                      {{ $warn === 'warnings' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                {{ __('reports.warnings.only') }}
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                             {{ $warn === 'warnings' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                    {{ $warningCount }}
                </span>
            </a>

            <div class="text-sm text-gray-500">
                {{ __('reports.notes.warnings_explain') }}
            </div>
        </div>
    </div>
@endif
