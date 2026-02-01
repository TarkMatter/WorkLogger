<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        @include('auth._register_fields')

        @include('auth._register_actions')
    </form>
</x-guest-layout>
