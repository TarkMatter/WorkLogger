<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            日報 編集（{{ $report->report_date->format('Y-m-d') }}）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($report->status === 'rejected' && $report->rejection_reason)
                        <div class="mb-6 p-4 border border-red-200 bg-red-50 rounded-lg">
                            <div class="text-sm font-semibold text-red-700">差戻し理由</div>
                            <div class="mt-2 whitespace-pre-wrap text-red-900">{{ $report->rejection_reason }}</div>
                        </div>
                    @endif

                    {{-- 更新フォーム（入力欄だけを囲う） --}}
                    <form id="report-update-form" method="POST" action="{{ route('reports.update', $report) }}">
                        @csrf
                        @method('PUT')

                        @include('reports._form', ['report' => $report])
                    </form>

                    {{-- 提出フォーム（CSRFだけでOK） --}}
                    @can('submit', $report)
                        <form id="report-submit-form" method="POST" action="{{ route('reports.submit', $report) }}"
                            onsubmit="return confirm('この日報を提出します。提出後は編集できません。よろしいですか？');">
                            @csrf
                        </form>
                    @endcan

                    {{-- ボタン行（横一列） --}}
                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button type="submit" form="report-update-form">更新</x-primary-button>

                        @can('submit', $report)
                            <x-primary-button type="submit" form="report-submit-form">提出</x-primary-button>
                        @endcan

                        <a href="{{ route('reports.show', $report) }}" class="inline-flex items-center px-4 py-2 border rounded-md">
                            詳細へ
                        </a>
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md">
                            一覧へ
                        </a>
                    </div>


                    <hr class="my-8 mt-3">

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">工数（Time Entries）</h3>

                        {{-- 追加フォーム --}}
                        <form method="POST" action="{{ route('reports.entries.store', $report) }}" class="space-y-4">
                            @csrf

                            <div>
                                <x-input-label for="project_id" value="案件" />
                                <select id="project_id" name="project_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">選択してください</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" @selected(old('project_id') == $p->id)>
                                            {{ $p->name }}{{ $p->code ? " ({$p->code})" : "" }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="task" value="作業内容（任意）" />
                                <x-text-input id="task" name="task" type="text" class="mt-1 block w-full"
                                    value="{{ old('task') }}" placeholder="例：構想設計、図面修正、レビュー対応 など" />
                                <x-input-error :messages="$errors->get('task')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="minutes" value="工数（分）" />
                                <x-text-input id="minutes" name="minutes" type="number" class="mt-1 block w-full"
                                    value="{{ old('minutes') }}" min="1" max="{{ 24*60 }}" required />
                                <x-input-error :messages="$errors->get('minutes')" class="mt-2" />
                                <div class="mt-1 text-sm text-gray-500">例：90（=1.5h） / 480（=8h）</div>
                            </div>

                            <x-primary-button>工数を追加</x-primary-button>
                        </form>

                        {{-- 一覧 --}}
                        @php
                            $total = $report->timeEntries->sum('minutes');
                            $hours = floor($total / 60);
                            $mins  = $total % 60;
                        @endphp

                        <div class="pt-4">
                            <div class="text-sm text-gray-500">合計</div>
                            <div class="text-lg font-semibold">{{ $hours }}h {{ $mins }}m（{{ $total }} 分）</div>
                        </div>

                        @if($report->timeEntries->count() === 0)
                            <p class="text-gray-700">まだ工数がありません。上のフォームから追加してください。</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="text-left border-b">
                                        <tr>
                                            <th class="py-2 pr-4">案件</th>
                                            <th class="py-2 pr-4">作業内容</th>
                                            <th class="py-2 pr-4">分</th>
                                            <th class="py-2 pr-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($report->timeEntries as $entry)
                                        <tr class="border-b">
                                            <td class="py-2 pr-4">{{ $entry->project->name }}</td>
                                            <td class="py-2 pr-4">{{ $entry->task ?? '-' }}</td>
                                            <td class="py-2 pr-4">{{ $entry->minutes }}</td>
                                            <td class="py-2 pr-4 text-right">
                                                <form method="POST" action="{{ route('reports.entries.destroy', [$report, $entry]) }}"
                                                    onsubmit="return confirm('この工数を削除します。よろしいですか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-700 hover:underline">削除</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('reports.destroy', $report) }}" class="mt-6"
                          onsubmit="return confirm('この日報を削除します。よろしいですか？');">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>削除</x-danger-button>
                    </form>

                    <div class="mt-8 text-sm text-gray-500">
                        工数（TimeEntry）入力と「提出/承認/差戻し」ボタンは次ステップで追加します。
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>