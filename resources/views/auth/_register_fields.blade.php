<!-- Name -->
<div>
    <x-input-label for="name" value="ユーザー名" />
    <x-text-input id="name" class="block mt-1 w-full"
                  type="text"
                  name="name"
                  value="{{ old('name') }}"
                  required
                  autofocus
                  autocomplete="name" />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<!-- Email Address -->
<div class="mt-4">
    <x-input-label for="email" value="メールアドレス" />
    <x-text-input id="email" class="block mt-1 w-full"
                  type="email"
                  name="email"
                  value="{{ old('email') }}"
                  required
                  autocomplete="username" />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<!-- Password -->
<div class="mt-4">
    <x-input-label for="password" value="パスワード" />
    <x-text-input id="password" class="block mt-1 w-full"
                  type="password"
                  name="password"
                  required
                  autocomplete="new-password" />
    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>

<!-- Confirm Password -->
<div class="mt-4">
    <x-input-label for="password_confirmation" value="パスワード（確認）" />
    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                  type="password"
                  name="password_confirmation"
                  required
                  autocomplete="new-password" />
    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
</div>
