<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">日報</h2>

            <a href="{{ route('reports.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                新規作成
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- flash messages --}}
                    @if (session('success'))
                        <div class="mb-4 p-3 border rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-3 border rounded-md">
                            {{ session('error') }}
                        </div>
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

                    {{-- tabs --}}
                    @php
                        $statusLabels = [
                            'all'       => 'すべて',
                            'draft'     => '下書き',
                            'submitted' => '提出済み（未処理）',
                            'approved'  => '承認済み',
                            'rejected'  => '差戻し',
                        ];

                        $current = $status ?? 'all';

                        $count = fn($key) => (int) ($counts[$key] ?? 0);

                        $tabs = auth()->user()->canApprove()
                            ? ['submitted', 'all', 'rejected', 'approved']
                            : ['all', 'draft', 'submitted', 'rejected', 'approved'];

                        $sort = $sort ?? 'report_date';
                        $dir  = $dir ?? 'desc';
                        $warn = $warn ?? 'all';
                        $warningCount = (int) ($warningCount ?? 0);
                    @endphp

                    <div class="mb-4 flex flex-wrap gap-2">
                        @foreach($tabs as $key)
                            @php
                                $isActive = $current === $key;

                                // submitted以外ではwarnは落とす
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

                    @if(auth()->user()->canApprove() && $current === 'submitted')
                        <div class="mb-2 text-sm text-gray-500">
                            承認待ち（提出済み）を表示しています。「処理する」または行クリックで承認パネルへ移動できます。
                        </div>

                        <div class="mb-4 flex flex-wrap items-center gap-2">
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
                                すべて
                            </a>

                            <a href="{{ $warnHref }}"
                               class="inline-flex items-center gap-2 px-3 py-1.5 border rounded-full text-sm
                                      {{ $warn === 'warnings' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                警告のみ
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs
                                             {{ $warn === 'warnings' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $warningCount }}
                                </span>
                            </a>

                            <div class="text-sm text-gray-500">
                                ※ 工数が <span class="font-semibold">0分</span> または <span class="font-semibold">24時間超</span> の場合、警告が表示されます。
                            </div>
                        </div>
                    @endif

                    {{-- sort controls (approver only) --}}
                    @if(auth()->user()->canApprove())
                        <div class="mb-4 p-3 border rounded-md bg-gray-50">
                            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-3">
                                <input type="hidden" name="status" value="{{ $current }}">
                                <input type="hidden" name="warn" value="{{ ($current === 'submitted') ? $warn : 'all' }}">

                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600">並び替え</span>
                                    <select name="sort" class="border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="report_date" @selected($sort === 'report_date')>日付</option>
                                        <option value="user_name" @selected($sort === 'user_name')>ユーザー名</option>
                                        <option value="total_minutes" @selected($sort === 'total_minutes')>合計工数</option>
                                    </select>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-600">順序</span>
                                    <select name="dir" class="border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="desc" @selected($dir === 'desc')>降順</option>
                                        <option value="asc" @selected($dir === 'asc')>昇順</option>
                                    </select>
                                </div>

                                <button class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm">
                                    適用
                                </button>

                                <a href="{{ route('reports.index', ['status' => $current, 'warn' => ($current === 'submitted') ? $warn : 'all']) }}"
                                   class="text-sm text-gray-600 hover:underline">
                                    リセット
                                </a>
                            </form>
                        </div>
                    @endif

                    {{-- table --}}
                    @if($reports->count() === 0)
                        <p>日報がありません。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left border-b">
                                    <tr>
                                        <th class="py-2 pr-4">日付</th>
                                        <th class="py-2 pr-4">状態</th>
                                        <th class="py-2 pr-4">合計</th>
                                        @if(auth()->user()->canApprove())
                                            <th class="py-2 pr-4">ユーザー</th>
                                        @endif
                                        <th class="py-2 pr-4 text-right">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($reports as $report)
                                    @php
                                        $label = match($report->status) {
                                            'draft' => '下書き',
                                            'submitted' => '提出済み',
                                            'approved' => '承認済み',
                                            'rejected' => '差戻し',
                                            default => $report->status,
                                        };

                                        $total = (int) ($report->total_minutes ?? 0);
                                        $h = intdiv($total, 60);
                                        $m = $total % 60;

                                        $warnZero = ($total === 0);
                                        $warnOver = ($total > 24 * 60);

                                        $isApprover = auth()->user()->canApprove();

                                        // ★ここが重要：実際に承認できるかは policy で判定
                                        $canReview = auth()->user()->can('approve', $report);

                                        $rowHref = $canReview
                                            ? route('reports.show', $report) . '#approval-panel'
                                            : route('reports.show', $report);

                                        $rowHover = 'hover:bg-amber-50';
                                        if ($warnZero) $rowHover = 'hover:bg-red-50';
                                        if ($warnOver) $rowHover = 'hover:bg-amber-100';
                                    @endphp

                                    <tr class="border-b {{ $canReview ? $rowHover . ' cursor-pointer' : '' }}"
                                        @if($canReview)
                                            role="link"
                                            tabindex="0"
                                            onclick="window.location.href={{ json_encode($rowHref) }};"
                                            onkeydown="if(event.key==='Enter' || event.key===' '){ event.preventDefault(); window.location.href={{ json_encode($rowHref) }}; }"
                                        @endif
                                    >
                                        <td class="py-2 pr-4">
                                            <a class="text-blue-700 hover:underline"
                                            href="{{ $rowHref }}"
                                            onclick="event.stopPropagation();">
                                                {{ $report->report_date->format('Y-m-d') }}
                                            </a>
                                        </td>

                                        <td class="py-2 pr-4">
                                            {{ $label }}

                                            @if($isApprover && $report->status === 'submitted' && ($warnZero || $warnOver))
                                                <div class="mt-1 flex flex-wrap gap-2">
                                                    @if($warnZero)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-red-50 text-red-800 border-red-200">
                                                            ⚠ 工数0
                                                        </span>
                                                    @endif
                                                    @if($warnOver)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-amber-50 text-amber-900 border-amber-200">
                                                            ⚠ 24h超
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>

                                        <td class="py-2 pr-4">
                                            {{ $h }}h {{ $m }}m
                                            <span class="text-gray-500">（{{ $total }}分）</span>
                                        </td>

                                        @if($isApprover)
                                            <td class="py-2 pr-4">
                                                {{ $report->user->name }}
                                                @if((int)$report->user_id === (int)auth()->id())
                                                    <span class="ml-2 text-xs text-gray-500">（自分）</span>
                                                @endif
                                            </td>
                                        @endif

                                        <td class="py-2 pr-4 text-right">
                                            @if($isApprover)
                                                @if($canReview)
                                                    <a href="{{ $rowHref }}"
                                                    onclick="event.stopPropagation();"
                                                    class="inline-flex items-center px-3 py-1.5 border rounded-md bg-gray-800 text-white hover:bg-gray-700">
                                                        処理する
                                                    </a>
                                                @else
                                                    <a href="{{ route('reports.show', $report) }}"
                                                    onclick="event.stopPropagation();"
                                                    class="text-gray-700 hover:underline">
                                                        詳細
                                                    </a>
                                                @endif
                                            @else
                                                @can('update', $report)
                                                    <a class="text-gray-700 hover:underline"
                                                    href="{{ route('reports.edit', $report) }}"
                                                    onclick="event.stopPropagation();">
                                                        編集
                                                    </a>
                                                @else
                                                    <a class="text-gray-700 hover:underline"
                                                    href="{{ route('reports.show', $report) }}"
                                                    onclick="event.stopPropagation();">
                                                        詳細
                                                    </a>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $reports->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
