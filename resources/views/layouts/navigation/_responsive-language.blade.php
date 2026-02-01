@php
    $supportedLocales = config('locales.supported', ['ja', 'en']);
    $currentLocale = app()->getLocale();
@endphp

<div class="mt-3 px-4">
    <div class="text-xs text-gray-400 mb-2">{{ __('nav.language') }}</div>

    <div class="flex flex-wrap gap-2">
        @foreach($supportedLocales as $loc)
            <form method="POST" action="{{ route('locale.set') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ $loc }}">
                <button type="submit"
                    class="px-3 py-2 border rounded-md text-sm
                    {{ $currentLocale === $loc ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 border-gray-300' }}">
                    {{ __('locales.' . $loc) }}
                </button>
            </form>
        @endforeach
    </div>
</div>
