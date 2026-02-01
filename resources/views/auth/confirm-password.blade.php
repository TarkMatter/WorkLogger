<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        @include('auth._confirm_password_fields')

        @include('auth._simple_submit', ['label' => __('Confirm')])
    </form>
</x-guest-layout>
