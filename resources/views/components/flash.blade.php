@php
    // New style: session('flash') = ['type' => 'success|error|warning|info', 'message' => '...']
    $flash = session('flash');

    // Backward compatibility (if some controllers still use with('success', ...) etc.)
    if (! $flash) {
        if (session('success')) {
            $flash = ['type' => 'success', 'message' => session('success')];
        } elseif (session('error')) {
            $flash = ['type' => 'error', 'message' => session('error')];
        } elseif (session('warning')) {
            $flash = ['type' => 'warning', 'message' => session('warning')];
        } elseif (session('info')) {
            $flash = ['type' => 'info', 'message' => session('info')];
        }
    }

    $type = $flash['type'] ?? null;
    $message = $flash['message'] ?? null;

    $classes = match ($type) {
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error'   => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-900',
        'info'    => 'border-blue-200 bg-blue-50 text-blue-800',
        default   => 'border-gray-200 bg-gray-50 text-gray-800',
    };
@endphp

@if($message)
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
        <div x-data="{ show: true }" x-show="show" x-transition
             class="border rounded-md px-4 py-3 flex items-start justify-between gap-3 {{ $classes }}">
            <div class="text-sm leading-5">
                {{ $message }}
            </div>

            <button type="button" class="text-lg leading-none opacity-70 hover:opacity-100"
                    @click="show = false" aria-label="close">
                ×
            </button>
        </div>
    </div>
@endif
