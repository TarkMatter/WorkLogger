<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                @include('layouts.navigation._logo')

                @include('layouts.navigation._primary-links')
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @include('layouts.navigation._language-dropdown')

                @include('layouts.navigation._user-dropdown')
            </div>

            @include('layouts.navigation._hamburger')
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        @include('layouts.navigation._responsive-links')

        @include('layouts.navigation._responsive-settings')
    </div>
</nav>
