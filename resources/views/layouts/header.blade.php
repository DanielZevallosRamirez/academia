@php use Illuminate\Support\Facades\Storage; @endphp
<header class="sticky top-0 z-30 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6">
    <!-- Left Side -->
    <div class="flex items-center gap-4">
        <!-- Mobile Menu Button -->
        <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Page Title -->
        <div>
            <h1 class="text-lg font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
            <p class="text-sm text-slate-500 hidden sm:block">@yield('page-description', '')</p>
        </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center gap-2 sm:gap-4">
        <!-- Search (Desktop) -->
        <div class="hidden md:block relative">
            <input type="text" placeholder="Buscar..." 
                   class="w-64 pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <!-- Notifications -->
        <button class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition-colors">
                <div data-header-avatar class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center overflow-hidden">
                    @if(auth()->user()->photo)
                        <img src="{{ Storage::url(auth()->user()->photo) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-white font-semibold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}</span>
                    @endif
                </div>
                <span class="hidden md:block text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                <svg class="hidden md:block w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 x-cloak
                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-200 py-2 z-50">
                
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Mi Perfil
                    </a>
                </div>

                <div class="border-t border-slate-100 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Cerrar Sesion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
