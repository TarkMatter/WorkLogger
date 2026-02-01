<main class="min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="container" style="max-width: 720px;">
        <div class="glass-card rounded-5 shadow-lg overflow-hidden">
            <div class="p-5 p-md-6 text-center">
                @php
                    $title = \Illuminate\Support\Facades\Lang::has('ui.app_title')
                        ? __('ui.app_title')
                        : '日報管理';

                    $dashboardLabel = \Illuminate\Support\Facades\Lang::has('nav.dashboard')
                        ? __('nav.dashboard')
                        : 'ダッシュボード';

                    $loginLabel = \Illuminate\Support\Facades\Lang::has('ui.login')
                        ? __('ui.login')
                        : 'ログイン';
                @endphp

                <h1 class="brand-title fw-bold mb-4"
                    style="font-size: clamp(2.6rem, 5vw, 4.2rem); line-height: 1.05;">
                    {{ $title }}
                </h1>

                <div class="mx-auto" style="max-width: 420px;">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="btn btn-brand btn-lg rounded-pill w-100 py-3 fw-semibold text-white">
                            {{ $dashboardLabel }}
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="btn btn-brand btn-lg rounded-pill w-100 py-3 fw-semibold text-white">
                            {{ $loginLabel }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</main>
