<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">案件</h2>

            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                新規作成
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($projects->count() === 0)
                        <p>まだ案件がありません。「新規作成」から追加してください。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left border-b">
                                    <tr>
                                        <th class="py-2 pr-4">案件名</th>
                                        <th class="py-2 pr-4">コード</th>
                                        <th class="py-2 pr-4">状態</th>
                                        <th class="py-2 pr-4">期間</th>
                                        <th class="py-2 pr-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">
                                            <a class="text-blue-700 hover:underline"
                                               href="{{ route('projects.show', $project) }}">
                                                {{ $project->name }}
                                            </a>
                                        </td>
                                        <td class="py-2 pr-4">{{ $project->code ?? '-' }}</td>
                                        <td class="py-2 pr-4">
                                            {{ $project->status === 'active' ? '稼働中' : 'アーカイブ' }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            @php
                                                $s = $project->starts_on?->format('Y-m-d');
                                                $e = $project->ends_on?->format('Y-m-d');
                                            @endphp
                                            {{ $s || $e ? ($s ?? '—') . ' 〜 ' . ($e ?? '—') : '-' }}
                                        </td>
                                        <td class="py-2 pr-4 text-right">
                                            <a class="text-gray-700 hover:underline"
                                               href="{{ route('projects.edit', $project) }}">
                                                編集
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $projects->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>