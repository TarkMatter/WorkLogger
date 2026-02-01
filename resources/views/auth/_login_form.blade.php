@php
    // i18n フォールバック（キーが無くても壊れない）
    $title = \Illuminate\Support\Facades\Lang::has('ui.login_title')
        ? __('ui.login_title')
        : 'ログイン';

    $emailLabel = \Illuminate\Support\Facades\Lang::has('ui.email')
        ? __('ui.email')
        : 'メールアドレス';

    $passwordLabel = \Illuminate\Support\Facades\Lang::has('ui.password')
        ? __('ui.password')
        : 'パスワード';

    $rememberLabel = \Illuminate\Support\Facades\Lang::has('ui.remember_me')
        ? __('ui.remember_me')
        : 'ログイン状態を保持';

    $forgotLabel = \Illuminate\Support\Facades\Lang::has('ui.forgot_password')
        ? __('ui.forgot_password')
        : 'パスワードをお忘れですか？';

    $loginLabel = \Illuminate\Support\Facades\Lang::has('ui.login')
        ? __('ui.login')
        : 'ログイン';

    $backToTop = \Illuminate\Support\Facades\Lang::has('common.back')
        ? __('common.back')
        : '戻る';
@endphp

<div class="text-center mb-4">
    <h1 class="fw-bold mb-1" style="font-size: clamp(2rem, 3.5vw, 2.6rem);">
        {{ $title }}
    </h1>
</div>

<div class="mb-3">
    <x-auth-session-status :status="session('status')" />
</div>

<form method="POST" action="{{ route('login') }}" class="vstack gap-3">
    @csrf

    <div>
        <label for="email" class="form-label fw-semibold">{{ $emailLabel }}</label>
        <input id="email"
               name="email"
               type="email"
               value="{{ old('email') }}"
               required
               autofocus
               autocomplete="username"
               class="form-control form-control-lg" />
        @if($errors->has('email'))
            <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
        @endif
    </div>

    <div>
        <label for="password" class="form-label fw-semibold">{{ $passwordLabel }}</label>
        <input id="password"
               name="password"
               type="password"
               required
               autocomplete="current-password"
               class="form-control form-control-lg" />
        @if($errors->has('password'))
            <div class="text-danger small mt-1">{{ $errors->first('password') }}</div>
        @endif
    </div>

    <div class="form-check">
        <input id="remember_me" name="remember" type="checkbox" class="form-check-input">
        <label class="form-check-label" for="remember_me">{{ $rememberLabel }}</label>
    </div>

    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mt-2">
        @if (Route::has('password.request'))
            <a class="text-decoration-none small"
               href="{{ route('password.request') }}"
               style="color: var(--brand-1);">
                {{ $forgotLabel }}
            </a>
        @else
            <span></span>
        @endif

        <button type="submit" class="btn btn-brand btn-lg rounded-pill px-4 text-white fw-semibold">
            {{ $loginLabel }}
        </button>
    </div>
</form>

<div class="text-center mt-4">
    <a href="{{ url('/') }}" class="text-decoration-none small text-secondary">
        ← {{ $backToTop }}
    </a>
</div>
