<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
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
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                {{-- Language Dropdown (separate) --}}
                <x-dropdown align="right" width="36">
                    <x-slot name="trigger">
                        @php
                            $locale = app()->getLocale();
                            $langLabel = \Illuminate\Support\Facades\Lang::has('nav.language')
                                ? __('nav.language')
                                : 'Language';

                            $currentLang = $locale === 'ja' ? '日本語' : 'English';
                        @endphp

                        <button type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                            <span class="me-2">{{ $currentLang }}</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ $langLabel }}
                        </div>

                        {{-- ja --}}
                        <form method="POST" action="{{ route('locale.set') }}">
                            @csrf
                            <input type="hidden" name="locale" value="ja">
                            <x-dropdown-link :href="route('locale.set')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="flex items-center justify-between">
                                    <span>日本語</span>
                                    @if(app()->getLocale() === 'ja')
                                        <span class="text-gray-500">✓</span>
                                    @endif
                                </div>
                            </x-dropdown-link>
                        </form>

                        {{-- en --}}
                        <form method="POST" action="{{ route('locale.set') }}">
                            @csrf
                            <input type="hidden" name="locale" value="en">
                            <x-dropdown-link :href="route('locale.set')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="flex items-center justify-between">
                                    <span>English</span>
                                    @if(app()->getLocale() === 'en')
                                        <span class="text-gray-500">✓</span>
                                    @endif
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                {{-- User Dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @php
                            $manageAccountLabel = \Illuminate\Support\Facades\Lang::has('nav.manage_account')
                                ? __('nav.manage_account')
                                : 'Manage Account';
                        @endphp

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ $manageAccountLabel }}
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('nav.profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('nav.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
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

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            {{-- Responsive Language (separate, not profile/logout) --}}
            <div class="mt-3 px-4">
                <div class="text-xs text-gray-400 mb-2">
                    {{ \Illuminate\Support\Facades\Lang::has('nav.language') ? __('nav.language') : 'Language' }}
                </div>

                <div class="flex gap-2">
                    <form method="POST" action="{{ route('locale.set') }}">
                        @csrf
                        <input type="hidden" name="locale" value="ja">
                        <button type="submit"
                            class="px-3 py-2 border rounded-md text-sm w-full
                            {{ app()->getLocale() === 'ja' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 border-gray-300' }}">
                            日本語
                        </button>
                    </form>

                    <form method="POST" action="{{ route('locale.set') }}">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button type="submit"
                            class="px-3 py-2 border rounded-md text-sm w-full
                            {{ app()->getLocale() === 'en' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 border-gray-300' }}">
                            English
                        </button>
                    </form>
                </div>
            </div>

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
        </div>
    </div>
</nav>
