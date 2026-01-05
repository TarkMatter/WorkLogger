<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                日報 詳細（{{ $report->report_date->format('Y-m-d') }}）
            </h2>

            @can('update', $report)
                <a href="{{ route('reports.edit', $report) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                    編集
                </a>
            @endcan

            {{-- @can('approve', $report)
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('reports.approve', ['dailyReport' => $report]) }}"
                            onsubmit="return confirm('この日報を承認します。よろしいですか？');">
                            @csrf
                            <x-primary-button>承認</x-primary-button>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('reports.reject', ['dailyReport' => $report]) }}"
                        class="space-y-2"
                        onsubmit="return confirm('この日報を差戻しします。よろしいですか？');">
                        @csrf

                        <div>
                            <x-input-label for="rejection_reason" value="差戻し理由（必須）" />
                            <textarea id="rejection_reason" name="rejection_reason" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                rows="3">{{ old('rejection_reason') }}</textarea>
                            <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                        </div>

                        <x-danger-button>差戻し</x-danger-button>
                    </form>
                </div>
            @endcan --}}

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 space-y-4">
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

                    @php
                        $statusMap = [
                            'draft'     => ['下書き',   'bg-gray-50 text-gray-900 border-gray-200'],
                            'submitted' => ['提出済み', 'bg-amber-50 text-amber-900 border-amber-200'],
                            'approved'  => ['承認済み', 'bg-emerald-50 text-emerald-900 border-emerald-200'],
                            'rejected'  => ['差戻し',   'bg-red-50 text-red-900 border-red-200'],
                        ];
                        [$statusLabel, $statusClass] = $statusMap[$report->status] ?? [$report->status, 'bg-gray-50 text-gray-900 border-gray-200'];
                    @endphp

                    <div class="mb-4 p-4 border rounded-lg {{ $statusClass }}">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-sm opacity-80">状態</div>
                                <div class="text-lg font-semibold">{{ $statusLabel }}</div>
                            </div>

                            <div class="text-sm opacity-80 text-right">
                                @if($report->submitted_at)
                                    <div>提出: {{ $report->submitted_at->format('Y-m-d H:i') }}</div>
                                @endif
                                @if($report->approved_at)
                                    <div>確認: {{ $report->approved_at->format('Y-m-d H:i') }}</div>
                                @endif
                            </div>
                        </div>

                        @if(in_array($report->status, ['approved','rejected'], true) && $report->approver)
                            <div class="mt-2 text-sm opacity-80">
                                {{ $report->status === 'approved' ? '承認者' : '差戻し者' }}: {{ $report->approver->name }}
                            </div>
                        @endif
                    </div>

                    @if($report->status === 'rejected' && $report->rejection_reason)
                        <div class="mb-4 p-4 border border-red-200 bg-red-50 rounded-lg">
                            <div class="text-sm font-semibold text-red-700">差戻し理由</div>
                            <div class="mt-2 whitespace-pre-wrap text-red-900">{{ $report->rejection_reason }}</div>

                            @can('update', $report)
                                <div class="mt-3">
                                    <a href="{{ route('reports.edit', $report) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                        修正して再提出する
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @endif


                    <div>
                        <div class="text-sm text-gray-500">状態</div>
                        @php
                            $label = match($report->status) {
                                'draft' => '下書き',
                                'submitted' => '提出済み',
                                'approved' => '承認済み',
                                'rejected' => '差戻し',
                                default => $report->status,
                            };
                        @endphp
                        <div class="font-semibold">{{ $label }}</div>

                    </div>

                    @can('approve', $report)
                        @if($report->status === 'submitted')
                            <div class="mt-4 p-4 border rounded-lg space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500">承認操作</div>
                                        <div class="font-semibold">この日報は提出済みです</div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            承認するか、理由を添えて差戻しできます。
                                        </div>
                                    </div>

                                    {{-- 承認ボタンは目立たせて右上 --}}
                                    <form method="POST"
                                        action="{{ route('reports.approve', ['dailyReport' => $report]) }}"
                                        onsubmit="return confirm('この日報を承認します。よろしいですか？');">
                                        @csrf
                                        <x-primary-button>承認</x-primary-button>
                                    </form>
                                </div>

                                {{-- 差戻しは折りたたみ：必要なときだけ開く --}}
                                <details class="border rounded-md">
                                    <summary class="cursor-pointer select-none px-3 py-2 text-sm font-semibold">
                                        差戻し（理由必須）
                                    </summary>

                                    <div class="px-3 pb-3 pt-2 space-y-2">
                                        <form method="POST"
                                            action="{{ route('reports.reject', ['dailyReport' => $report]) }}"
                                            class="space-y-2"
                                            onsubmit="return confirm('この日報を差戻しします。よろしいですか？');">
                                            @csrf

                                            <div>
                                                <x-input-label for="rejection_reason" value="差戻し理由（必須 / 1000文字以内）" />
                                                <textarea id="rejection_reason"
                                                        name="rejection_reason"
                                                        required
                                                        maxlength="1000"
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                                        rows="4"
                                                        placeholder="例：案件PJ-001の工数内訳が不明確です。作業内容を追記してください。">{{ old('rejection_reason') }}</textarea>
                                                <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                                            </div>

                                            <div class="flex items-center justify-end gap-2">
                                                <a href="#" class="text-sm text-gray-500 hover:underline"
                                                onclick="this.closest('details').removeAttribute('open'); return false;">
                                                    閉じる
                                                </a>
                                                <x-danger-button>差戻し</x-danger-button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        @endif
                    @endcan


                    @if($report->approved_by)
                        <div>
                            <div class="text-sm text-gray-500">{{ $report->status === 'approved' ? '承認者' : '確認者（差戻し）' }}</div>
                            <div class="font-semibold">
                                {{ $report->approver?->name ?? '不明' }}
                                @if($report->approved_at)
                                    （{{ $report->approved_at->format('Y-m-d H:i') }}）
                                @endif
                            </div>
                        </div>
                    @endif


                    @if(auth()->user()->canApprove())
                        <div>
                            <div class="text-sm text-gray-500">ユーザー</div>
                            <div class="font-semibold">{{ $report->user->name }}</div>
                        </div>
                    @endif

                    <div>
                        <div class="text-sm text-gray-500">メモ</div>
                        <div class="whitespace-pre-wrap">{{ $report->memo ?? '-' }}</div>
                    </div>

                    @if($report->status === 'rejected' && $report->rejection_reason)
                        <div class="p-3 border rounded-md">
                            <div class="text-sm text-gray-500">差戻し理由</div>
                            <div class="whitespace-pre-wrap font-semibold">{{ $report->rejection_reason }}</div>
                        </div>
                    @endif


                    <hr class="my-6">
                    @php
                        $total = $report->timeEntries->sum('minutes');
                        $hours = intdiv($total, 60);
                        $mins  = $total % 60;
                    @endphp

                    <div class="space-y-3">
                        <h3 class="text-lg font-semibold">工数</h3>

                        <div>
                            <div class="text-sm text-gray-500">合計</div>
                            <div class="text-lg font-semibold">{{ $hours }}h {{ $mins }}m（{{ $total }} 分）</div>
                        </div>

                        @if($report->timeEntries->count() === 0)
                            <p class="text-gray-700">工数がありません。</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-left border-b">
                                        <tr>
                                            <th class="py-2 pr-4">案件</th>
                                            <th class="py-2 pr-4">作業内容</th>
                                            <th class="py-2 pr-4">分</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($report->timeEntries as $entry)
                                        <tr class="border-b">
                                            <td class="py-2 pr-4">{{ $entry->project->name }}</td>
                                            <td class="py-2 pr-4">{{ $entry->task ?? '-' }}</td>
                                            <td class="py-2 pr-4">{{ $entry->minutes }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>


                    <div class="pt-4">
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md">
                            一覧へ
                        </a>
                    </div>

                    <div class="text-sm text-gray-500">
                        工数の表示は次のステップで追加します。
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>