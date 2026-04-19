<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6">
    <!-- Mobile Menu Button -->
    <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700">
        <i data-lucide="menu" class="w-6 h-6"></i>
    </button>

    <!-- Page Title -->
    <h1 class="text-lg font-semibold text-gray-900 hidden lg:block">
        @yield('page-title', 'Dashboard')
    </h1>

    <!-- Right Side -->
    <div class="flex items-center gap-4">
        <!-- Search -->
        <div class="hidden md:block relative">
            <input type="text" placeholder="Buscar..." 
                   class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
        </div>

        <!-- Notifications -->
        <button class="relative text-gray-500 hover:text-gray-700">
            <i data-lucide="bell" class="w-5 h-5"></i>
            @if(auth()->user()->isEstudiante() && auth()->user()->getPendingPaymentsCount() > 0)
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                    {{ auth()->user()->getPendingPaymentsCount() }}
                </span>
            @endif
        </button>

        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 text-sm">
                <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" 
                     class="w-8 h-8 rounded-full object-cover">
                <span class="hidden md:block text-gray-700">{{ auth()->user()->name }}</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden md:block"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak
                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <i data-lucide="user" class="w-4 h-4"></i>
                    Mi Perfil
                </a>
                <hr class="my-1">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Cerrar Sesion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
