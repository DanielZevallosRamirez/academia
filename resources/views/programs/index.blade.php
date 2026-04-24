@extends('layouts.app')

@section('title', 'Programas')
@section('page-title', 'Gestion de Programas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Programas Academicos</h1>
            <p class="text-gray-500">Administra los programas, cursos y contenidos</p>
        </div>
        <a href="{{ route('programs.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Nuevo Programa
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar programa..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                </div>
            </div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg">
                <option value="">Todos</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Programs Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Image -->
                <div class="h-40 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                    @if($program->image)
                        <img src="{{ $program->image_url }}" alt="{{ $program->name }}" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="graduation-cap" class="w-16 h-16 text-white/50"></i>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $program->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $program->duration_months }} meses</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            {{ $program->status === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $program->status === 'activo' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    @if($program->description)
                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $program->description }}</p>
                    @endif

                    @if($program->teacher)
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                        <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-3 h-3 text-indigo-600"></i>
                        </div>
                        <span class="font-medium">Profesor:</span> {{ $program->teacher->name }}
                    </div>
                    @endif

                    <!-- Stats -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-4 text-gray-500">
                            <span class="flex items-center gap-1">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                                {{ $program->courses_count }} cursos
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="users" class="w-4 h-4"></i>
                                {{ $program->enrollments_count }}
                            </span>
                        </div>
                        <span class="font-semibold text-emerald-600">S/ {{ number_format($program->price, 2) }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('programs.show', $program) }}" 
                           class="flex-1 py-2 text-center text-sm font-medium text-emerald-600 border border-emerald-600 rounded-lg hover:bg-emerald-50">
                            Ver Detalles
                        </a>
                        <a href="{{ route('programs.edit', $program) }}" 
                           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="{{ route('programs.destroy', $program) }}" 
                              onsubmit="return confirm('¿Eliminar este programa?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center">
                <i data-lucide="book-open" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                <p class="text-gray-500">No hay programas registrados</p>
                <a href="{{ route('programs.create') }}" class="mt-2 text-emerald-600 hover:text-emerald-700">
                    Crear primer programa
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($programs->hasPages())
        <div class="flex justify-center">
            {{ $programs->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
