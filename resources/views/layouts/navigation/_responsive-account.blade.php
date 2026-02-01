<div class="mt-3 space-y-1">
    <x-responsive-nav-link :href="route('profile.edit')">
        {{ __('nav.profile') }}
    </x-responsive-nav-link>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <x-responsive-nav-link :href="route('logout')"
            onclick="event.preventDefault(); this.closest('form').submit();">
            {{ __('nav.logout') }}
        </x-responsive-nav-link>
    </form>
</div>
