<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">案件 新規作成</h2>
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

                    <form method="POST" action="{{ route('projects.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="code" value="案件コード（必須）" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                                          value="{{ old('code') }}" required />
                        </div>

                        <div>
                            <x-input-label for="name" value="案件名（必須）" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          value="{{ old('name') }}" required />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" value="開始日" />
                                <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full"
                                              value="{{ old('start_date') }}" />
                            </div>

                            <div>
                                <x-input-label for="end_date" value="終了日" />
                                <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full"
                                              value="{{ old('end_date') }}" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" value="説明" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                      rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('projects.index') }}" class="text-gray-600 hover:underline">戻る</a>
                            <x-primary-button>作成</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
