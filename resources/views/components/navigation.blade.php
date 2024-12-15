<div class="relative z-50 bg-gray-100 bg-center sm:flex sm:justify-center sm:items-center bg-dots dark:bg-gray-900 selection:bg-indigo-500 selection:text-white">
        @if (Route::has('login'))
            <div class="flex items-center p-6 text-right sm:fixed sm:top-0 sm:right-0">
                @auth
                    <x-dropdown text="Menu">
                        <x-slot:header>
                            <p>Welcome, {{ Auth::user()->username }}!</p>
                        </x-slot:header>
                        <a href="{{route('profile')}}">
                            <x-dropdown.items icon="user" text="Profile and settings" separator />
                        </a>
                        <a href="{{route('events')}}">
                            <x-dropdown.items icon="calendar" text="View Events" separator/>
                        </a>
                        <a href="{{route('attending')}}">
                            <x-dropdown.items icon="queue-list" text="Visited Events" separator/>
                        </a>
                        <a href="{{route('purchases')}}">
                            <x-dropdown.items icon="credit-card" text="Purchases" separator/>
                        </a>
                        <a href="{{route('favorites')}}">
                            <x-dropdown.items icon="heart" text="Favorites" separator/>
                        </a>
                        <x-dropdown.items icon="arrow-left-on-rectangle" text="Logout" separator
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();" />
                    </x-dropdown>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
