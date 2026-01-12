<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">案件 編集</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">

                    @if ($errors->any())
                        <div class="p-3 border rounded-md">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 更新フォーム（ボタンは下のフッターに集約するので、ここには置かない） --}}
                    <form id="project-update-form"
                          method="POST"
                          action="{{ route('projects.update', $project) }}"
                          class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="code" value="案件コード（必須）" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                                          value="{{ old('code', $project->code) }}" required />
                        </div>

                        <div>
                            <x-input-label for="name" value="案件名（必須）" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          value="{{ old('name', $project->name) }}" required />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" value="開始日" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full"
                                              value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" />
                            </div>

                            <div>
                                <x-input-label for="end_date" value="終了日" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full"
                                              value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" value="説明" />
                            <textarea id="description"
                                      name="description"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                      rows="4">{{ old('description', $project->description) }}</textarea>
                        </div>
                    </form>

                    {{-- 削除フォーム（ボタンは下のフッターから送信する） --}}
                    <form id="project-delete-form"
                          method="POST"
                          action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('この案件を削除します。よろしいですか？');">
                        @csrf
                        @method('DELETE')
                    </form>

                    {{-- フッターバー（ボタンの見た目・位置を統一） --}}
                    <div class="pt-4 border-t flex items-center justify-between">
                        <x-danger-button form="project-delete-form" type="submit">
                            削除
                        </x-danger-button>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('projects.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                                戻る
                            </a>

                            <x-primary-button form="project-update-form" type="submit">
                                更新
                            </x-primary-button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
