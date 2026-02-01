@props([
    'value',
    'type' => 'datetime', // 'datetime' | 'date'
    'timezone' => null,
])

@php
    $locale = app()->getLocale();

    $formats = config('locales.formats', []);
    $fallbackLocale = config('locales.fallback', config('app.fallback_locale', 'en'));

    $format = $formats[$type][$locale]
        ?? $formats[$type][$fallbackLocale]
        ?? ($type === 'date' ? 'Y-m-d' : 'Y-m-d H:i');

    $tz = $timezone ?? config('app.timezone');

    $dt = null;
    if ($value instanceof \DateTimeInterface) {
        $dt = \Illuminate\Support\Carbon::instance($value);
    } elseif (!is_null($value) && $value !== '') {
        $dt = \Illuminate\Support\Carbon::parse($value);
    }

    if ($dt) {
        $dt = $dt->timezone($tz)->locale($locale);
    }
@endphp

{{ $dt ? $dt->translatedFormat($format) : '' }}
