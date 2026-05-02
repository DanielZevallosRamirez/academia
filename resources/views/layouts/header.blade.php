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

        <!-- Notifications Dropdown -->
        <div x-data="notificationDropdown()" x-init="init()" class="relative">
            <button @click="toggle()" class="relative p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" 
                      class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1"></span>
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
                 class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg shadow-slate-200/50 border border-slate-200 z-50 overflow-hidden">
                
                <!-- Header -->
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-semibold text-slate-800">Notificaciones</h3>
                    <button x-show="unreadCount > 0" @click="markAllAsRead()" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">
                        Marcar todo como leido
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-96 overflow-y-auto">
                    <template x-if="loading">
                        <div class="py-8 text-center">
                            <svg class="animate-spin h-6 w-6 text-slate-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </template>

                    <template x-if="!loading && notifications.length === 0">
                        <div class="py-8 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p class="text-sm text-slate-500">No tienes notificaciones</p>
                        </div>
                    </template>

                    <template x-for="notification in notifications" :key="notification.id">
                        <a :href="notification.link || '#'" 
                           @click="markAsRead(notification)"
                           :class="{ 'bg-emerald-50/50': !notification.read_at }"
                           class="block px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition-colors">
                            <div class="flex gap-3">
                                <div :class="getIconClass(notification.type)" class="flex-shrink-0 w-8 h-8 rounded-full bg-current/10 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="getIconSvg(notification.type)"></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-800" x-text="notification.title"></p>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2" x-text="notification.message"></p>
                                    <p class="text-xs text-slate-400 mt-1" x-text="formatTime(notification.created_at)"></p>
                                </div>
                                <div x-show="!notification.read_at" class="flex-shrink-0">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full block"></span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
                    <a href="{{ route('notifications.all') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium flex items-center justify-center gap-1">
                        Ver todas las notificaciones
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <script>
            function notificationDropdown() {
                return {
                    open: false,
                    loading: false,
                    notifications: [],
                    unreadCount: 0,

                    init() {
                        this.fetchNotifications();
                        // Poll for new notifications every 30 seconds
                        setInterval(() => this.fetchUnreadCount(), 30000);
                    },

                    toggle() {
                        this.open = !this.open;
                        if (this.open) {
                            this.fetchNotifications();
                        }
                    },

                    async fetchNotifications() {
                        this.loading = true;
                        try {
                            const response = await fetch('{{ route("notifications.index") }}', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const data = await response.json();
                            this.notifications = data.notifications;
                            this.unreadCount = data.unread_count;
                        } catch (error) {
                            console.error('Error fetching notifications:', error);
                        } finally {
                            this.loading = false;
                        }
                    },

                    async fetchUnreadCount() {
                        try {
                            const response = await fetch('{{ route("notifications.unread-count") }}', {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const data = await response.json();
                            this.unreadCount = data.count;
                        } catch (error) {
                            console.error('Error fetching unread count:', error);
                        }
                    },

                    async markAsRead(notification) {
                        if (!notification.read_at) {
                            try {
                                await fetch(`/notifications/${notification.id}/read`, {
                                    method: 'POST',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });
                                notification.read_at = new Date().toISOString();
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                            } catch (error) {
                                console.error('Error marking notification as read:', error);
                            }
                        }
                    },

                    async markAllAsRead() {
                        try {
                            await fetch('{{ route("notifications.mark-all-read") }}', {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            this.notifications.forEach(n => n.read_at = new Date().toISOString());
                            this.unreadCount = 0;
                        } catch (error) {
                            console.error('Error marking all as read:', error);
                        }
                    },

                    getIconClass(type) {
                        const classes = {
                            'enrollment': 'text-emerald-500',
                            'payment': 'text-blue-500',
                            'attendance': 'text-purple-500',
                            'user': 'text-amber-500',
                            'program': 'text-indigo-500',
                            'system': 'text-slate-500'
                        };
                        return classes[type] || 'text-slate-500';
                    },

                    getIconSvg(type) {
                        const icons = {
                            'enrollment': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>',
                            'payment': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                            'attendance': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>',
                            'user': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
                            'program': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
                            'system': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        };
                        return icons[type] || icons['system'];
                    },

                    formatTime(dateString) {
                        const date = new Date(dateString);
                        const now = new Date();
                        const diff = Math.floor((now - date) / 1000);

                        if (diff < 60) return 'Hace un momento';
                        if (diff < 3600) return `Hace ${Math.floor(diff / 60)} minutos`;
                        if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} horas`;
                        if (diff < 604800) return `Hace ${Math.floor(diff / 86400)} dias`;
                        
                        return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
                    }
                }
            }
        </script>

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
