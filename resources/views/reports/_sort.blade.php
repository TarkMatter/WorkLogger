@if(auth()->user()->canApprove())
    <div class="p-3 border rounded-md bg-gray-50">
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="status" value="{{ $current }}">
            <input type="hidden" name="warn" value="{{ ($current === 'submitted') ? $warn : 'all' }}">

            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">{{ __('reports.labels.sort') }}</span>
                <select name="sort" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="report_date" @selected($sort === 'report_date')>{{ __('reports.sort.date') }}</option>
                    <option value="user_name" @selected($sort === 'user_name')>{{ __('reports.sort.user_name') }}</option>
                    <option value="total_minutes" @selected($sort === 'total_minutes')>{{ __('reports.sort.total_minutes') }}</option>
                </select>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-600">{{ __('reports.labels.order') }}</span>
                <select name="dir" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="desc" @selected($dir === 'desc')>{{ __('reports.sort.desc') }}</option>
                    <option value="asc" @selected($dir === 'asc')>{{ __('reports.sort.asc') }}</option>
                </select>
            </div>

            <button class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm">
                {{ __('common.apply') }}
            </button>

            <a href="{{ route('reports.index', ['status' => $current, 'warn' => ($current === 'submitted') ? $warn : 'all']) }}"
               class="text-sm text-gray-600 hover:underline">
                {{ __('common.reset') }}
            </a>
        </form>
    </div>
@endif
