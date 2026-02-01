<div class="pt-2 pb-3 space-y-1">
    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('nav.dashboard') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.*')">
        {{ __('nav.projects') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
        {{ __('nav.reports') }}
    </x-responsive-nav-link>

    @if(auth()->user()?->isAdmin())
        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
            {{ __('nav.permissions') }}
        </x-responsive-nav-link>
    @endif
</div>
