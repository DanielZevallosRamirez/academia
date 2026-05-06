@extends('layouts.app')

@section('title', 'Gestion de Permisos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion de Permisos</h1>
            <p class="text-gray-600 mt-1">Configura los permisos para cada rol del sistema</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Leyenda de Roles -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <h3 class="font-medium text-gray-900 mb-3">Roles del Sistema</h3>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                <span class="text-sm text-gray-700">Administrador - Acceso completo al sistema</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-sm text-gray-700">Secretario(a) - Atencion, matriculas y pagos</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-cyan-500"></span>
                <span class="text-sm text-gray-700">Administrativo - Gestion y reportes</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-sm text-gray-700">Profesor - Gestion de clases y asistencia</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-sm text-gray-700">Estudiante - Acceso al portal de aprendizaje</span>
            </div>
        </div>
    </div>

    <!-- Tabla de Permisos -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 min-w-[300px]">
                            Permiso
                        </th>
                        @foreach($roles as $role)
                            @php
                                $headerStyles = [
                                    'admin' => 'text-purple-700 bg-purple-50',
                                    'secretario' => 'text-amber-700 bg-amber-50',
                                    'administrativo' => 'text-cyan-700 bg-cyan-50',
                                    'profesor' => 'text-blue-700 bg-blue-50',
                                    'estudiante' => 'text-emerald-700 bg-emerald-50',
                                ];
                            @endphp
                            <th class="px-6 py-4 text-center text-sm font-semibold min-w-[130px] {{ $headerStyles[$role] ?? '' }}">
                                <div class="flex flex-col items-center gap-1">
                                    <span>{{ $roleNames[$role] }}</span>
                                    <form action="{{ route('permissions.reset') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $role }}">
                                        <button type="submit" class="text-xs font-normal text-gray-500 hover:text-gray-700 underline">
                                            Restablecer
                                        </button>
                                    </form>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($groupedPermissions as $module => $permissions)
                        <!-- Module Header -->
                        <tr class="bg-gray-100">
                            <td colspan="{{ count($roles) + 1 }}" class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    @php
                                        $moduleIcons = [
                                            'dashboard' => 'layout-dashboard',
                                            'programs' => 'book-open',
                                            'students' => 'users',
                                            'professors' => 'user-cog',
                                            'payments' => 'credit-card',
                                            'attendance' => 'calendar-check',
                                            'sessions' => 'video',
                                            'reports' => 'bar-chart-3',
                                            'notifications' => 'bell',
                                            'settings' => 'settings',
                                            'portal' => 'graduation-cap',
                                        ];
                                    @endphp
                                    <i data-lucide="{{ $moduleIcons[$module] ?? 'folder' }}" class="w-5 h-5 text-gray-600"></i>
                                    <span class="font-semibold text-gray-900">{{ $moduleNames[$module] ?? ucfirst($module) }}</span>
                                </div>
                            </td>
                        </tr>
                        
                        @foreach($permissions as $permission)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="pl-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                        @if($permission->description)
                                            <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                        @endif
                                    </div>
                                </td>
                                @foreach($roles as $role)
                                    @php
                                        $isActive = $rolePermissions[$role]->where('permission_id', $permission->id)->where('is_active', true)->isNotEmpty();
                                        $cellStyles = [
                                            'admin' => 'bg-purple-50/50',
                                            'secretario' => 'bg-amber-50/50',
                                            'administrativo' => 'bg-cyan-50/50',
                                            'profesor' => 'bg-blue-50/50',
                                            'estudiante' => 'bg-emerald-50/50',
                                        ];
                                        $toggleStyles = [
                                            'admin' => 'peer-focus:ring-purple-300 peer-checked:bg-purple-600',
                                            'secretario' => 'peer-focus:ring-amber-300 peer-checked:bg-amber-600',
                                            'administrativo' => 'peer-focus:ring-cyan-300 peer-checked:bg-cyan-600',
                                            'profesor' => 'peer-focus:ring-blue-300 peer-checked:bg-blue-600',
                                            'estudiante' => 'peer-focus:ring-emerald-300 peer-checked:bg-emerald-600',
                                        ];
                                    @endphp
                                    <td class="px-6 py-4 text-center {{ $cellStyles[$role] ?? '' }}">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   class="sr-only peer permission-toggle"
                                                   data-role="{{ $role }}"
                                                   data-permission-id="{{ $permission->id }}"
                                                   {{ $isActive ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 
                                                        {{ $toggleStyles[$role] ?? '' }}
                                                        rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                                                        peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] 
                                                        after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full 
                                                        after:h-5 after:w-5 after:transition-all"></div>
                                        </label>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen de Permisos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($roles as $role)
            @php
                $activeCount = $roleActiveCounts[$role];
                $percentage = $totalPermissions > 0 ? round(($activeCount / $totalPermissions) * 100) : 0;
                $counterColors = [
                    'admin' => 'text-purple-600',
                    'secretario' => 'text-amber-600',
                    'administrativo' => 'text-cyan-600',
                    'profesor' => 'text-blue-600',
                    'estudiante' => 'text-emerald-600',
                ];
                $barColors = [
                    'admin' => 'bg-purple-600',
                    'secretario' => 'bg-amber-600',
                    'administrativo' => 'bg-cyan-600',
                    'profesor' => 'bg-blue-600',
                    'estudiante' => 'bg-emerald-600',
                ];
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">{{ $roleNames[$role] }}</h4>
                    <span data-role-counter="{{ $role }}" class="text-sm {{ $counterColors[$role] ?? '' }}">
                        {{ $activeCount }}/{{ $totalPermissions }} permisos
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div data-role-progress="{{ $role }}" class="h-2 rounded-full transition-all duration-300 {{ $barColors[$role] ?? '' }}"
                        style="width: {{ $percentage }}%"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contadores por rol
    const roleCounts = {
        @foreach($roles as $role)
        '{{ $role }}': { active: {{ $roleActiveCounts[$role] }}, total: {{ $totalPermissions }} },
        @endforeach
    };

    function updateRoleCounter(role) {
        const count = roleCounts[role];
        const percentage = count.total > 0 ? Math.round((count.active / count.total) * 100) : 0;
        
        // Actualizar texto del contador
        const counterText = document.querySelector(`[data-role-counter="${role}"]`);
        if (counterText) {
            counterText.textContent = `${count.active}/${count.total} permisos`;
        }
        
        // Actualizar barra de progreso
        const progressBar = document.querySelector(`[data-role-progress="${role}"]`);
        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
        }
    }

    // Toggle individual de permisos
    document.querySelectorAll('.permission-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const role = this.dataset.role;
            const permissionId = this.dataset.permissionId;
            const isActive = this.checked;
            const toggleElement = this;

            // Deshabilitar temporalmente el toggle
            toggleElement.disabled = true;

            fetch('{{ route("permissions.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    role: role,
                    permission_id: permissionId,
                    is_active: isActive
                })
            })
            .then(response => response.json())
            .then(data => {
                toggleElement.disabled = false;
                
                if (data.success) {
                    // Actualizar contador
                    if (isActive) {
                        roleCounts[role].active++;
                    } else {
                        roleCounts[role].active--;
                    }
                    updateRoleCounter(role);
                    
                    // Mostrar feedback visual
                    const row = toggleElement.closest('tr');
                    row.classList.add('bg-emerald-50');
                    setTimeout(() => {
                        row.classList.remove('bg-emerald-50');
                    }, 300);

                    // Toast de confirmacion
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: isActive ? 'Permiso activado' : 'Permiso desactivado',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                } else {
                    // Revertir si fallo
                    toggleElement.checked = !isActive;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo actualizar el permiso'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toggleElement.disabled = false;
                toggleElement.checked = !isActive;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexion. Intenta de nuevo.'
                });
            });
        });
    });
});
</script>
@endpush
@endsection
