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
                    @if($reports->count() === 0)
                        <p>日報がありません。「新規作成」から作ってみましょう。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left border-b">
                                    <tr>
                                        <th class="py-2 pr-4">日付</th>
                                        <th class="py-2 pr-4">状態</th>
                                        @if(auth()->user()->canApprove())
                                            <th class="py-2 pr-4">ユーザー</th>
                                        @endif
                                        <th class="py-2 pr-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($reports as $report)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">
                                            <a class="text-blue-700 hover:underline"
                                               href="{{ route('reports.show', $report) }}">
                                                {{ $report->report_date->format('Y-m-d') }}
                                            </a>
                                        </td>
                                        <td class="py-2 pr-4">
                                            @php
                                                $label = match($report->status) {
                                                    'draft' => '下書き',
                                                    'submitted' => '提出済み',
                                                    'approved' => '承認済み',
                                                    'rejected' => '差戻し',
                                                    default => $report->status,
                                                };
                                            @endphp
                                            {{ $label }}
                                        </td>

                                        @if(auth()->user()->canApprove())
                                            <td class="py-2 pr-4">{{ $report->user->name }}</td>
                                        @endif

                                        <td class="py-2 pr-4 text-right">
                                            @can('update', $report)
                                                <a class="text-gray-700 hover:underline"
                                                   href="{{ route('reports.edit', $report) }}">
                                                    編集
                                                </a>
                                            @else
                                                <span class="text-gray-400">編集不可</span>
                                            @endcan
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