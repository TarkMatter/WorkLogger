<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">案件 詳細</h2>

            <a href="{{ route('projects.edit', $project) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                編集
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-3">
                    <div>
                        <div class="text-sm text-gray-500">案件名</div>
                        <div class="text-lg font-semibold">{{ $project->name }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">コード</div>
                            <div>{{ $project->code ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">状態</div>
                            <div>{{ $project->status === 'active' ? '稼働中' : 'アーカイブ' }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">期間</div>
                        <div>
                            {{ $project->starts_on?->format('Y-m-d') ?? '—' }}
                            〜
                            {{ $project->ends_on?->format('Y-m-d') ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">説明</div>
                        <div class="whitespace-pre-wrap">{{ $project->description ?? '-' }}</div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md">
                            一覧へ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>