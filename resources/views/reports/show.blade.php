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
                            @php
                                $total = (int) $report->timeEntries->sum('minutes');
                                $hours = intdiv($total, 60);
                                $mins  = $total % 60;

                                $warnZero = ($total === 0);
                                $warnOver = ($total > 24 * 60);

                                // 警告があるなら承認はUIでも無効化（サーバ側でも弾いてる）
                                $approveDisabled = ($warnZero || $warnOver);

                                $approveDisabledReason = $warnZero
                                    ? '工数が0分のため承認できません（未入力の可能性）。'
                                    : ($warnOver ? '合計工数が24時間を超えているため承認できません（異常値の可能性）。' : null);

                                // 差戻しフォームを開いた状態にしたい条件
                                $openReject = old('rejection_reason') || $errors->has('rejection_reason') || $warnZero || $warnOver;

                                // 差戻し理由が空（trim後に空）なら差戻しボタン無効
                                $initialReason = (string) old('rejection_reason', '');
                                $rejectDisabled = trim($initialReason) === '';
                                $initialCounter = mb_strlen($initialReason);
                            @endphp

                            <div id="approval-panel" class="mt-4 p-4 border rounded-lg space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm text-gray-500">承認操作</div>
                                        <div class="font-semibold">この日報は提出済みです</div>

                                        <div class="mt-1 text-sm text-gray-600">
                                            合計工数：<span class="font-semibold">{{ $hours }}h {{ $mins }}m</span>（{{ $total }}分）
                                        </div>

                                        @if($warnZero || $warnOver)
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @if($warnZero)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-red-50 text-red-800 border-red-200">
                                                        ⚠ 工数0（未入力の可能性）
                                                    </span>
                                                @endif
                                                @if($warnOver)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border bg-amber-50 text-amber-900 border-amber-200">
                                                        ⚠ 24h超（異常値の可能性）
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-2 text-sm text-gray-700">
                                                {{ $approveDisabledReason }}
                                                差戻しで修正依頼するのがおすすめです。
                                            </div>
                                        @else
                                            <div class="mt-2 text-sm text-gray-500">
                                                承認するか、理由を添えて差戻しできます。
                                            </div>
                                        @endif
                                    </div>

                                    {{-- 承認ボタン（警告があるときは無効化） --}}
                                    <form method="POST"
                                        action="{{ route('reports.approve', ['dailyReport' => $report]) }}"
                                        onsubmit="return {{ $approveDisabled ? 'false' : "confirm('この日報を承認します。よろしいですか？')" }};">
                                        @csrf

                                        <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                                {{ $approveDisabled ? 'disabled' : '' }}
                                                title="{{ $approveDisabled ? $approveDisabledReason : '' }}">
                                            承認
                                        </button>

                                        @if($approveDisabled)
                                            <div class="mt-2 text-xs text-gray-500 text-right">
                                                ※ 警告があるため承認は無効です
                                            </div>
                                        @endif
                                    </form>
                                </div>

                                {{-- 差戻し --}}
                                <details class="border rounded-md" @if($openReject) open @endif>
                                    <summary class="cursor-pointer select-none px-3 py-2 text-sm font-semibold">
                                        差戻し（理由必須）
                                    </summary>

                                    <div class="px-3 pb-3 pt-2 space-y-3">

                                        {{-- テンプレ（よくある指摘） --}}
                                        <div class="p-3 border rounded-md bg-gray-50">
                                            <div class="text-xs font-semibold text-gray-600 mb-2">よくある指摘（クリックで追記）</div>

                                            <div class="flex flex-wrap gap-2">
                                                <button type="button"
                                                        class="tpl-btn inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm"
                                                        data-text="工数の内訳が不明確です。各行の「作業内容」をもう少し具体的に追記してください。">
                                                    内訳が不明確
                                                </button>

                                                <button type="button"
                                                        class="tpl-btn inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm"
                                                        data-text="案件の選択が誤っている可能性があります。正しい案件に付け替えて再提出してください。">
                                                    案件が違う
                                                </button>

                                                <button type="button"
                                                        class="tpl-btn inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm"
                                                        data-text="合計工数が24時間を超えています。入力ミスがないか確認して修正してください。">
                                                    24h超
                                                </button>

                                                <button type="button"
                                                        class="tpl-btn inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm"
                                                        data-text="メモが不足しています。背景／判断理由／成果（何ができたか）を追記してください。">
                                                    メモ不足
                                                </button>

                                                <button type="button"
                                                        class="tpl-btn inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm"
                                                        data-text="工数が0分になっています。工数の入力を確認して再提出してください。">
                                                    工数0
                                                </button>

                                                <button type="button"
                                                        id="tpl-clear"
                                                        class="inline-flex items-center px-3 py-1.5 border rounded-md bg-white hover:bg-gray-50 text-sm text-gray-700">
                                                    クリア
                                                </button>
                                            </div>

                                            <div class="mt-2 text-xs text-gray-500">
                                                ※ 既に入力がある場合は末尾に追記します（誤って消さないため）
                                            </div>
                                        </div>

                                        <form method="POST"
                                            action="{{ route('reports.reject', ['dailyReport' => $report]) }}"
                                            class="space-y-2"
                                            onsubmit="return confirm('この日報を差戻しします。よろしいですか？');">
                                            @csrf

                                            <div>
                                                <label for="rejection_reason" class="text-sm font-semibold text-gray-700">
                                                    差戻し理由（必須 / 1000文字以内）
                                                </label>

                                                <textarea id="rejection_reason"
                                                        name="rejection_reason"
                                                        required
                                                        maxlength="1000"
                                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                                        rows="5"
                                                        placeholder="例：工数の内訳が不明確です。作業内容を追記してください。">{{ $initialReason }}</textarea>

                                                <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                                                    <span id="reject-hint">※ 空（空白のみ）は送信できません</span>
                                                    <span id="rejection-counter">{{ $initialCounter }}/1000</span>
                                                </div>

                                                @if($errors->has('rejection_reason'))
                                                    <div class="mt-2 text-sm text-red-600">
                                                        {{ $errors->first('rejection_reason') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex items-center justify-end gap-2">
                                                <a href="#" class="text-sm text-gray-500 hover:underline"
                                                onclick="this.closest('details').removeAttribute('open'); return false;">
                                                    閉じる
                                                </a>

                                                <button type="submit"
                                                        id="reject-button"
                                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        {{ $rejectDisabled ? 'disabled' : '' }}
                                                        title="{{ $rejectDisabled ? '差戻し理由を入力してください（空白のみは不可）' : '' }}">
                                                    差戻し
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const textarea = document.getElementById('rejection_reason');
                                    const rejectBtn = document.getElementById('reject-button');
                                    const counter = document.getElementById('rejection-counter');
                                    const clearBtn = document.getElementById('tpl-clear');

                                    if (!textarea || !rejectBtn) return;

                                    const update = () => {
                                        const raw = textarea.value ?? '';
                                        const trimmed = raw.trim();

                                        const ok = trimmed.length > 0;

                                        rejectBtn.disabled = !ok;
                                        rejectBtn.title = ok ? '' : '差戻し理由を入力してください（空白のみは不可）';

                                        if (counter) counter.textContent = `${raw.length}/1000`;
                                    };

                                    const appendTemplate = (text) => {
                                        const raw = textarea.value ?? '';
                                        const next = raw.trim().length > 0 ? (raw.replace(/\s*$/, '') + "\n" + text) : text;
                                        textarea.value = next;
                                        textarea.focus();
                                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                                    };

                                    document.querySelectorAll('.tpl-btn').forEach((btn) => {
                                        btn.addEventListener('click', () => {
                                            const text = btn.getAttribute('data-text') || '';
                                            if (text) appendTemplate(text);
                                        });
                                    });

                                    if (clearBtn) {
                                        clearBtn.addEventListener('click', () => {
                                            textarea.value = '';
                                            textarea.focus();
                                            textarea.dispatchEvent(new Event('input', { bubbles: true }));
                                        });
                                    }

                                    textarea.addEventListener('input', update);
                                    update();
                                });
                            </script>
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