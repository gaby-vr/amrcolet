<nav x-data="{ open: false }" id="header" class="fixed w-full leading-normal tracking-normal bg-white text-white border-b border-gray-100 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a id="nav-toggle" class="text-white" href="{{ route('home') }}">
                        <x-jet-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 lg:flex">
                    <span class="inline-flex rounded-md">
                        <x-jet-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                            {{ __('Acasa') }}
                        </x-jet-nav-link>
                    </span>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 lg:flex">
                    <span class="inline-flex rounded-md">
                        <x-jet-nav-link href="{{ route('order.index') }}" :active="request()->routeIs('order.index')">
                            {{ __('New order') }}
                        </x-jet-nav-link>
                    </span>
                </div>

                {{-- <div class="hidden space-x-8 sm:-my-px sm:ml-10 lg:flex">
                    <span class="inline-flex rounded-md">
                        <x-jet-nav-link href="javascript:void(0)" :active="false">
                            {{ __('Prices') }}
                        </x-jet-nav-link>
                    </span>
                </div> --}}

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 lg:flex">
                    <x-jet-dropdown align="right" width="60" flex="true">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md">
                                <x-jet-nav-link href="javascript:void(0)" :active="request()->is('info/*') || request()->routeIs('terms.show') || request()->routeIs('policy.show')">
                                    {{ __('Info') }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </x-jet-nav-link>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-60">
                                <x-jet-dropdown-link href="{{ route('terms.show') }}">
                                    {{ __('Terms of Service') }}
                                </x-jet-dropdown-link>
                                <x-jet-dropdown-link href="{{ route('packaging.show') }}">
                                    {{ __('Packaging Policy') }}
                                </x-jet-dropdown-link>
                                <x-jet-dropdown-link href="{{ route('policy.show') }}">
                                    {{ __('Privacy Policy') }}
                                </x-jet-dropdown-link>
                                <x-jet-dropdown-link href="{{ route('cookies.show') }}">
                                    {{ __('Cookie Policy') }}
                                </x-jet-dropdown-link>
                                <x-jet-dropdown-link href="{{ route('postal.show') }}">
                                    {{ __('Postal Conditions') }}
                                </x-jet-dropdown-link>
                                <x-jet-dropdown-link href="{{ route('contact') }}">
                                    {{ __('Contact') }}
                                </x-jet-dropdown-link>
                            </div>
                        </x-slot>
                    </x-jet-dropdown>
                </div>

                @auth
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 lg:flex">
                    <span class="inline-flex rounded-md">
                        <x-jet-nav-link href="{{ route('dashboard.show') }}" :active="request()->routeIs('dashboard.show')">
                            {{ __('Dashboard') }}
                        </x-jet-nav-link>
                    </span>
                </div>
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 lg:flex">
                    <x-jet-nav-link href="{{ route('dashboard.announcement') }}" :active="request()->routeIs('dashboard.announcement')">
                        {{ __('Anunt') }}
                        @if(auth()->user()->announcement_seen == '0' || auth()->user()->announcement_seen == '')
                        <div class="flex justify-center items-center m-1 font-medium py-1 px-2 bg-white rounded-full text-yellow-700 bg-yellow-100 border border-yellow-300 ">
                            <div class="text-xs font-normal leading-none max-w-full flex-initial">Nou</div>
                        </div>
                        @endif
                    </x-jet-nav-link>
                </div>
                @endauth
            </div>

            <div class="hidden lg:flex lg:items-center sm:ml-6" style="min-width: 205px;">
                @guest
                <div class="ml-3 relative">
                    <span class="inline-flex rounded-md">
                        <x-jet-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white focus:bg-gray-100 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition">
                            {{ __('Login') }}
                        </x-jet-nav-link>
                    </span>

                    <span class="inline-flex rounded-md">
                        <x-jet-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')" class="inline-flex items-center px-3 py-2 border border-b-1 border-blue-200 text-sm leading-4 font-medium rounded-md text-gray-500 bg-white focus:bg-gray-100 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition">
                            {{ __('Register') }}
                        </x-jet-nav-link>
                    </span>
                </div>
                @else

                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <!-- Teams Dropdown -->
                    <div class="ml-3 relative">
                        <x-jet-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                        {{ auth()->user()->currentTeam->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-jet-dropdown-link href="{{ route('teams.show', auth()->user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-jet-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-jet-dropdown-link>
                                    @endcan

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Team Switcher -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Switch Teams') }}
                                    </div>

                                    @foreach (auth()->user()->allTeams() as $team)
                                        <x-jet-switchable-team :team="$team" />
                                    @endforeach
                                </div>
                            </x-slot>
                        </x-jet-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                        {{ auth()->user()->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            {{-- <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-jet-dropdown-link> --}}

                            @if (auth()->user()->is_admin == 1)
                                <x-jet-dropdown-link href="{{ route('admin.view') }}">
                                    {{ __('Admin') }}
                                </x-jet-dropdown-link>
                            @endif

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-jet-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}"
                                         onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-jet-dropdown-link>
                            </form>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
                @endguest
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center lg:hidden">
                <div class="space-y-1 w-36 text-center lg:hidden">
                    <x-jet-nav-link href="{{ route('order.index') }}" :active="request()->routeIs('order.index')" class="inline-flex items-center px-3 py-2 border border-b-1 border-blue-200 text-sm leading-4 font-medium rounded-md text-gray-500 bg-white focus:bg-gray-100 hover:bg-gray-100 hover:text-gray-700 focus:outline-none transition">
                        {{ __('New order') }}
                    </x-jet-nav-link>
                </div>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="space-y-1">
            <x-jet-responsive-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                {{ __('Acasa') }}
            </x-jet-responsive-nav-link>
        </div>
        
        {{-- <div class="space-y-1">
            <x-jet-responsive-nav-link href="javascript:void(0)" :active="false">
                {{ __('Prices') }}
            </x-jet-responsive-nav-link>
        </div> --}}
        <div class="pt-2 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div>
                    <div class="font-medium text-base text-gray-800">{{ __('Info') }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-jet-responsive-nav-link href="{{ route('terms.show') }}" :active="request()->routeIs('terms.show')">
                    {{ __('Terms of Service') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('packaging.show') }}" :active="request()->routeIs('packaging.show')">
                    {{ __('Packaging Policy') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('policy.show') }}" :active="request()->routeIs('policy.show')">
                    {{ __('Privacy Policy') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('cookies.show') }}" :active="request()->routeIs('terms.show')">
                    {{ __('Cookie Policy') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')">
                    {{ __('Contact') }}
                </x-jet-responsive-nav-link>
            </div>
        </div>
        @auth
        <div class="pt-2 border-t space-y-1">
            <x-jet-responsive-nav-link href="{{ route('dashboard.show') }}" :active="request()->routeIs('dashboard.show')">
                {{ __('Dashboard') }}
            </x-jet-responsive-nav-link>
        </div>
        <div class="pt-2 space-y-1">
            <x-jet-responsive-nav-link href="{{ route('dashboard.announcement') }}" :active="request()->routeIs('dashboard.announcement')">
                {{ __('Anunt') }}
                @if(auth()->user()->announcement_seen == '0' || auth()->user()->announcement_seen == '')
                <div class="inline-flex justify-center items-center m-1 font-medium py-1 px-2 bg-white rounded-full text-yellow-700 bg-yellow-100 border border-yellow-300">
                    <div class="text-xs font-normal leading-none max-w-full flex-initial">Nou</div>
                </div>
                @endif
            </x-jet-responsive-nav-link>
        </div>
        @else
        <div class="pt-2 border-t space-y-1">
            <x-jet-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                {{ __('Login') }}
            </x-jet-responsive-nav-link>
        </div>
        <div class="space-y-1">
            <x-jet-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                {{ __('Register') }}
            </x-jet-responsive-nav-link>
        </div>
        @endauth

        @auth
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="flex-shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                {{-- <x-jet-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-jet-responsive-nav-link> --}}

                @if (auth()->user()->is_admin == 1)
                    <x-jet-responsive-nav-link href="{{ route('admin.view') }}" :active="request()->routeIs('admin.view')">
                        {{ __('Admin') }}
                    </x-jet-responsive-nav-link>
                @endif

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-jet-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-jet-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-jet-responsive-nav-link href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-jet-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-jet-responsive-nav-link href="{{ route('teams.show', auth()->user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-jet-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-jet-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-jet-responsive-nav-link>
                    @endcan

                    <div class="border-t border-gray-200"></div>

                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Switch Teams') }}
                    </div>

                    @foreach (auth()->user()->allTeams() as $team)
                        <x-jet-switchable-team :team="$team" component="jet-responsive-nav-link" />
                    @endforeach
                @endif
            </div>
        </div>
        @endauth
    </div>
</nav>
