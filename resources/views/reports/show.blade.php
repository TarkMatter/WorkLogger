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

            @can('approve', $report)
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('reports.approve', ['dailyReport' => $report]) }}"
                        onsubmit="return confirm('この日報を承認します。よろしいですか？');">
                        @csrf
                        <x-primary-button>承認</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('reports.reject', ['dailyReport' => $report]) }}"
                        onsubmit="return confirm('この日報を差戻しします。よろしいですか？');">
                        @csrf
                        <x-danger-button>差戻し</x-danger-button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                @if (session('success'))
                    <div class="mb-4 p-3 border rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="p-6 space-y-4">
                    <div>
                        <div class="text-sm text-gray-500">状態</div>
                        <div class="font-semibold">{{ $report->status }}</div>
                    </div>

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