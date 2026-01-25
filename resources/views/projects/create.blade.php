<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('projects.create_title') }}</h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:underline">
                ← {{ __('common.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 p-3 border rounded-md">
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
                            <label class="text-sm font-semibold text-gray-700" for="name">{{ __('projects.labels.name') }}</label>
                            <input id="name" name="name" type="text"
                                   value="{{ old('name') }}"
                                   required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-700" for="description">{{ __('projects.labels.description') }}</label>
                            <textarea id="description" name="description" rows="4"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('projects.index') }}"
                               class="inline-flex items-center px-4 py-2 border rounded-md bg-white hover:bg-gray-50">
                                {{ __('common.cancel') }}
                            </a>
                            <button class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                {{ __('common.create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
