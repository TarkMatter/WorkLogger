<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        @include('auth._reset_fields')

        @include('auth._simple_submit', ['label' => __('Reset Password')])
    </form>
</x-guest-layout>
