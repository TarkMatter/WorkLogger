<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">案件 編集</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('projects.update', $project) }}">
                        @csrf
                        @method('PUT')

                        @include('projects._form', ['project' => $project])

                        <div class="mt-6 flex items-center gap-3">
                            <x-primary-button>更新</x-primary-button>

                            <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-4 py-2 border rounded-md">
                                戻る
                            </a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="mt-6"
                          onsubmit="return confirm('この案件を削除します。よろしいですか？');">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>削除</x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>