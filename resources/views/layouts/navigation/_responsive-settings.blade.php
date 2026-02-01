<div class="pt-4 pb-1 border-t border-gray-200">
    <div class="px-4">
        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
    </div>

    @include('layouts.navigation._responsive-language')

    @include('layouts.navigation._responsive-account')
</div>
