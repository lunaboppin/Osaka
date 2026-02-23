<nav x-data="{ open: false }" class="bg-osaka-charcoal shadow-lg sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 group">
                        <img src="{{ asset('images/osaka.png') }}" alt="Osaka" class="block h-9 w-auto group-hover:scale-110 transition-transform duration-200" />
                        <span class="text-osaka-cream font-bold text-lg hidden sm:inline">Osaka</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('dashboard') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    @auth
                        <a href="{{ route('pins.create') }}"
                           class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('pins.create') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Add Pin
                        </a>
                        <a href="{{ route('pins.index') }}"
                           class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('pins.index') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            View Pins
                        </a>
                        <a href="{{ route('reminders.index') }}"
                           class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('reminders.*') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Reminders
                        </a>
                        @if(Auth::user()->hasPermission('admin.access'))
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Users
                            </a>
                            <a href="{{ route('admin.roles.index') }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Roles
                            </a>
                        @endif
                    @endauth
                    @guest
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-3 py-2 text-sm font-medium text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5 rounded-md transition-all duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Login
                        </a>
                    @endguest
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-osaka-cream/80 hover:text-osaka-cream hover:bg-white/5 focus:outline-none transition-all duration-200">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="" class="w-7 h-7 rounded-full mr-2 border-2 border-osaka-gold/50">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-osaka-gold mr-2 flex items-center justify-center text-xs font-bold text-osaka-charcoal">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="fill-current h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.show', Auth::user())">
                                {{ __('View Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>
                            @if(Auth::user()->hasPermission('admin.access'))
                                <div class="border-t border-gray-100 my-1"></div>
                                <x-dropdown-link :href="route('admin.roles.index')">
                                    {{ __('Manage Roles') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.users.index')">
                                    {{ __('Manage Users') }}
                                </x-dropdown-link>
                            @endif
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-osaka-cream/60 hover:text-osaka-cream hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-osaka-charcoal-light border-t border-white/10">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            @auth
                <a href="{{ route('pins.create') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('pins.create') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Pin
                </a>
                <a href="{{ route('pins.index') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('pins.index') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    View Pins
                </a>
                <a href="{{ route('reminders.index') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('reminders.*') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Reminders
                </a>
                @if(Auth::user()->hasPermission('admin.access'))
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Users
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.roles.*') ? 'text-osaka-gold bg-white/10' : 'text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Roles
                    </a>
                @endif
            @endauth
            @guest
                <a href="{{ route('login') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Login
                </a>
            @endguest
        </div>

        @auth
            <div class="pt-4 pb-3 border-t border-white/10 px-4">
                <div class="flex items-center mb-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="" class="w-8 h-8 rounded-full mr-3 border-2 border-osaka-gold/50">
                    @else
                        <div class="w-8 h-8 rounded-full bg-osaka-gold mr-3 flex items-center justify-center text-sm font-bold text-osaka-charcoal">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-medium text-sm text-osaka-cream">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-xs text-osaka-cream/50">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center px-3 py-2 rounded-md text-sm font-medium text-osaka-cream/70 hover:text-osaka-cream hover:bg-white/5">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
