@php
    $supportedLocales = config('locales.supported', ['ja', 'en']);
    $currentLocale = app()->getLocale();
    $width = 250;
@endphp

<x-dropdown align="right" width="48" menuWidthPx="{{ $width }}">
    <x-slot name="trigger">
        <button type="button"
            style="min-width: {{ $width }}px;"
            class="inline-flex items-center justify-between px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
            <span class="me-2">{{ __('locales.' . $currentLocale) }}</span>
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="block px-4 py-2 text-xs text-gray-400">
            {{ __('nav.language') }}
        </div>

        @foreach($supportedLocales as $loc)
            <form method="POST" action="{{ route('locale.set') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ $loc }}">

                <x-dropdown-link :href="route('locale.set')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    <div class="flex items-center justify-between">
                        <span>{{ __('locales.' . $loc) }}</span>
                        @if($currentLocale === $loc)
                            <span class="text-gray-500">✓</span>
                        @endif
                    </div>
                </x-dropdown-link>
            </form>
        @endforeach
    </x-slot>
</x-dropdown>
