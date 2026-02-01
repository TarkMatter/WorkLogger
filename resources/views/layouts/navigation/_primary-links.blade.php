<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('nav.dashboard') }}
    </x-nav-link>

    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
        {{ __('nav.projects') }}
    </x-nav-link>

    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
        {{ __('nav.reports') }}
    </x-nav-link>

    @if(auth()->user()?->isAdmin())
        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
            {{ __('nav.permissions') }}
        </x-nav-link>
    @endif
</div>
