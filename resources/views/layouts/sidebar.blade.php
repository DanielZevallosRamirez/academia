<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
    <!-- Logo -->
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
            </div>
            <span class="font-bold text-lg">Academia</span>
        </a>
        <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->user()->isAdmin())
            <!-- Admin Menu -->
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Administracion</p>
            </div>

            <a href="{{ route('students.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('students.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Estudiantes</span>
            </a>

            <a href="{{ route('programs.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('programs.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span>Programas</span>
            </a>

            <a href="{{ route('payments.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('payments.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="credit-card" class="w-5 h-5"></i>
                <span>Pagos</span>
            </a>

            <a href="{{ route('attendance.index') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('attendance.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="calendar-check" class="w-5 h-5"></i>
                <span>Asistencia</span>
            </a>
        @endif

        @if(auth()->user()->isProfesor())
            <!-- Profesor Menu -->
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Mis Clases</p>
            </div>

            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span>Mis Sesiones</span>
            </a>

            <a href="#" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-slate-300 hover:bg-slate-800 hover:text-white">
                <i data-lucide="scan-line" class="w-5 h-5"></i>
                <span>Escanear QR</span>
            </a>
        @endif

        @if(auth()->user()->isEstudiante())
            <!-- Estudiante Menu -->
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Mi Portal</p>
            </div>

            <a href="{{ route('estudiante.my-program') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('estudiante.my-program') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span>Mi Programa</span>
            </a>

            <a href="{{ route('estudiante.my-payments') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('estudiante.my-payments') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="receipt" class="w-5 h-5"></i>
                <span>Mis Pagos</span>
            </a>

            <a href="{{ route('estudiante.my-attendance') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('estudiante.my-attendance') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="calendar-check" class="w-5 h-5"></i>
                <span>Mi Asistencia</span>
            </a>

            <a href="{{ route('estudiante.my-qr') }}" 
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('estudiante.my-qr') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="qr-code" class="w-5 h-5"></i>
                <span>Mi Codigo QR</span>
            </a>
        @endif
    </nav>

    <!-- User Info -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700">
        <div class="flex items-center gap-3">
            <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" 
                 class="w-10 h-10 rounded-full object-cover">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-white" title="Cerrar sesion">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
