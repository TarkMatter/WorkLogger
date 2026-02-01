{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __('auth.login_title') ?? 'Login' }} - {{ config('app.name', 'Laravel') }}</title>

    {{-- Vite（Laravel welcome の流儀） --}}
    @vite(['resources/js/app.js'])

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @include('auth._login_styles')
</head>

<body class="text-dark">
<main class="min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="container" style="max-width: 560px;">
        <div class="glass-card rounded-5 shadow-lg overflow-hidden">
            <div class="p-5 p-md-6">

                @include('auth._login_form')

            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
