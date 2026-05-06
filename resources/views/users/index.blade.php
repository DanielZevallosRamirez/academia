@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Gestion de Usuarios')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Usuarios</h1>
            <p class="text-gray-500">Administra todos los usuarios del sistema</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Nuevo Usuario
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <a href="{{ route('users.create', ['role' => 'estudiante']) }}" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="graduation-cap" class="w-4 h-4 text-emerald-600"></i>
                        Estudiante
                    </a>
                    <a href="{{ route('users.create', ['role' => 'profesor']) }}" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="book-open" class="w-4 h-4 text-blue-600"></i>
                        Profesor
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="{{ route('users.create', ['role' => 'secretario']) }}" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-amber-600"></i>
                        Secretario(a)
                    </a>
                    <a href="{{ route('users.create', ['role' => 'administrativo']) }}" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="briefcase" class="w-4 h-4 text-cyan-600"></i>
                        Administrativo
                    </a>
                    <a href="{{ route('users.create', ['role' => 'admin']) }}" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="shield" class="w-4 h-4 text-purple-600"></i>
                        Administrador
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gray-100 rounded-lg">
                    <i data-lucide="users" class="w-5 h-5 text-gray-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500">Total</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <i data-lucide="graduation-cap" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['estudiantes'] }}</p>
                    <p class="text-xs text-gray-500">Estudiantes</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['profesores'] }}</p>
                    <p class="text-xs text-gray-500">Profesores</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-amber-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['secretarios'] }}</p>
                    <p class="text-xs text-gray-500">Secretarios</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-cyan-100 rounded-lg">
                    <i data-lucide="briefcase" class="w-5 h-5 text-cyan-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-cyan-600">{{ $stats['administrativos'] }}</p>
                    <p class="text-xs text-gray-500">Administrativos</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i data-lucide="shield" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['admins'] }}</p>
                    <p class="text-xs text-gray-500">Admins</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['activos'] }}</p>
                    <p class="text-xs text-gray-500">Activos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por nombre, email, DNI..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>
            </div>
            <select name="role" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Todos los roles</option>
                @foreach(\App\Models\User::ROLES as $value => $label)
                    <option value="{{ $value }}" {{ request('role') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Todos los estados</option>
                <option value="activo" {{ request('status') === 'activo' ? 'selected' : '' }}>Activos</option>
                <option value="inactivo" {{ request('status') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                <option value="suspendido" {{ request('status') === 'suspendido' ? 'selected' : '' }}>Suspendidos</option>
            </select>
            <select name="program_id" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Todos los programas</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Filtrar
            </button>
            @if(request()->hasAny(['search', 'role', 'status', 'program_id']))
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programa/Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }} {{ $user->last_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->dni ?? 'Sin DNI' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleStyles = [
                                        'admin' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'shield'],
                                        'secretario' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'clipboard-list'],
                                        'administrativo' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'icon' => 'briefcase'],
                                        'profesor' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'book-open'],
                                        'estudiante' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'graduation-cap'],
                                    ];
                                    $style = $roleStyles[$user->role] ?? $roleStyles['estudiante'];
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full {{ $style['bg'] }} {{ $style['text'] }}">
                                    <i data-lucide="{{ $style['icon'] }}" class="w-3 h-3"></i>
                                    {{ \App\Models\User::getRoleName($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $user->email }}</p>
                                <p class="text-sm text-gray-500">{{ $user->phone ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->role === 'estudiante')
                                    @if($user->enrollments->first())
                                        <span class="text-sm text-gray-900">{{ $user->enrollments->first()->program->name }}</span>
                                        @php $pending = $user->getPendingPaymentsCount(); @endphp
                                        @if($pending > 0)
                                            <p class="text-xs text-amber-600">{{ $pending }} pago(s) pendiente(s)</p>
                                        @else
                                            <p class="text-xs text-green-600">Pagos al dia</p>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400">Sin programa</span>
                                    @endif
                                @elseif($user->role === 'profesor')
                                    <span class="text-sm text-gray-600">{{ $user->specialty ?? 'Sin especialidad' }}</span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $user->status === 'activo' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $user->status === 'inactivo' ? 'bg-gray-100 text-gray-700' : '' }}
                                    {{ $user->status === 'suspendido' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" title="Ver">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if($user->role === 'estudiante')
                                        <a href="{{ route('users.qr', $user) }}" 
                                           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" title="Codigo QR">
                                            <i data-lucide="qr-code" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" title="Editar">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                              onsubmit="return confirm('¿Estas seguro de eliminar este usuario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg" title="Eliminar">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i data-lucide="users" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No se encontraron usuarios</p>
                                <a href="{{ route('users.create') }}" class="mt-2 text-emerald-600 hover:text-emerald-700 text-sm">
                                    Registrar primer usuario
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
