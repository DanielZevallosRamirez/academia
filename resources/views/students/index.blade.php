@extends('layouts.app')

@section('title', 'Estudiantes')
@section('page-title', 'Gestion de Estudiantes')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Estudiantes</h1>
            <p class="text-gray-500">Administra los estudiantes de la academia</p>
        </div>
        <a href="{{ route('students.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Nuevo Estudiante
        </a>
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
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Todos los estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
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
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagos</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $student->photo_url }}" alt="{{ $student->name }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $student->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $student->dni ?? 'Sin DNI' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $student->email }}</p>
                                <p class="text-sm text-gray-500">{{ $student->phone ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($student->enrollments->first())
                                    <span class="text-sm text-gray-900">{{ $student->enrollments->first()->program->name }}</span>
                                @else
                                    <span class="text-sm text-gray-400">Sin programa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $student->status === 'activo' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $student->status === 'inactivo' ? 'bg-gray-100 text-gray-700' : '' }}
                                    {{ $student->status === 'suspendido' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php $pending = $student->getPendingPaymentsCount(); @endphp
                                @if($pending > 0)
                                    <span class="text-sm text-amber-600">{{ $pending }} pendiente(s)</span>
                                @else
                                    <span class="text-sm text-green-600">Al dia</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('students.show', $student) }}" 
                                       class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" title="Ver">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('students.qr', $student) }}" 
                                       class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" title="Codigo QR">
                                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" 
                                       class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg" title="Editar">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('students.destroy', $student) }}" 
                                          onsubmit="return confirm('¿Estas seguro de eliminar este estudiante?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg" title="Eliminar">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i data-lucide="users" class="w-12 h-12 mx-auto text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No se encontraron estudiantes</p>
                                <a href="{{ route('students.create') }}" class="mt-2 text-emerald-600 hover:text-emerald-700 text-sm">
                                    Registrar primer estudiante
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $students->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
