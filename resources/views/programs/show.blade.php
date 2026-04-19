@extends('layouts.app')

@section('title', $program->name)
@section('page-title', 'Detalle de Programa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('programs.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Volver
        </a>
        <a href="{{ route('programs.edit', $program) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
            <i data-lucide="edit" class="w-4 h-4"></i>
            Editar Programa
        </a>
    </div>

    <!-- Program Info -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="h-48 bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
            @if($program->image)
                <img src="{{ $program->image_url }}" alt="{{ $program->name }}" class="w-full h-full object-cover">
            @else
                <i data-lucide="graduation-cap" class="w-24 h-24 text-white/50"></i>
            @endif
        </div>
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $program->name }}</h1>
                    <p class="text-gray-500 mt-1">{{ $program->description }}</p>
                </div>
                <span class="px-3 py-1 text-sm font-medium rounded-full 
                    {{ $program->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $program->is_active ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="mt-4 flex items-center gap-6 text-sm">
                <span class="flex items-center gap-2 text-gray-500">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    {{ $program->duration_months }} meses
                </span>
                <span class="flex items-center gap-2 text-gray-500">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                    {{ $program->courses->count() }} cursos
                </span>
                <span class="flex items-center gap-2 text-gray-500">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    {{ $program->active_students_count }} estudiantes
                </span>
                <span class="flex items-center gap-2 font-semibold text-emerald-600">
                    S/ {{ number_format($program->price, 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Courses (Malla Curricular) -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Malla Curricular</h2>
            <button onclick="document.getElementById('add-course-modal').classList.remove('hidden')"
                    class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Agregar Curso
            </button>
        </div>

        <div class="p-6 space-y-4">
            @forelse($program->courses as $course)
                <div class="border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                    <!-- Course Header -->
                    <div class="bg-gray-50 p-4 flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <span class="font-bold text-emerald-600">{{ $loop->iteration }}</span>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $course->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $course->modules->count() }} modulos - {{ $course->total_contents }} contenidos</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $course->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $course->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </div>
                    </div>

                    <!-- Modules -->
                    <div x-show="open" x-collapse class="border-t border-gray-200">
                        @foreach($course->modules as $module)
                            <div class="border-b border-gray-100 last:border-0" x-data="{ moduleOpen: false }">
                                <!-- Module Header -->
                                <div class="p-4 pl-16 flex items-center justify-between cursor-pointer hover:bg-gray-50" @click="moduleOpen = !moduleOpen">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-slate-100 rounded flex items-center justify-center">
                                            <span class="text-sm font-medium text-slate-600">{{ $loop->iteration }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $module->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $module->contents->count() }} contenidos</p>
                                        </div>
                                    </div>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': moduleOpen }"></i>
                                </div>

                                <!-- Contents -->
                                <div x-show="moduleOpen" x-collapse class="bg-gray-50 border-t border-gray-100">
                                    @foreach($module->contents as $content)
                                        <div class="p-3 pl-24 flex items-center gap-3 hover:bg-gray-100">
                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                                {{ $content->type === 'pdf' ? 'bg-red-100 text-red-600' : '' }}
                                                {{ $content->type === 'video' ? 'bg-blue-100 text-blue-600' : '' }}
                                                {{ $content->type === 'audio' ? 'bg-purple-100 text-purple-600' : '' }}
                                                {{ $content->type === 'link' ? 'bg-green-100 text-green-600' : '' }}
                                                {{ $content->type === 'text' ? 'bg-gray-100 text-gray-600' : '' }}">
                                                <i data-lucide="{{ $content->icon }}" class="w-4 h-4"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $content->title }}</p>
                                                @if($content->duration_minutes)
                                                    <p class="text-xs text-gray-500">{{ $content->duration_minutes }} min</p>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-400 uppercase">{{ $content->type }}</span>
                                        </div>
                                    @endforeach

                                    <!-- Add Content -->
                                    <button onclick="openAddContentModal({{ $module->id }})" 
                                            class="w-full p-3 pl-24 text-left text-sm text-emerald-600 hover:bg-emerald-50 flex items-center gap-2">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                        Agregar contenido
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <!-- Add Module -->
                        <button onclick="openAddModuleModal({{ $course->id }})"
                                class="w-full p-4 pl-16 text-left text-sm text-emerald-600 hover:bg-emerald-50 flex items-center gap-2 border-t border-gray-100">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Agregar modulo
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i data-lucide="book-open" class="w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                    <p>No hay cursos en este programa</p>
                    <button onclick="document.getElementById('add-course-modal').classList.remove('hidden')"
                            class="mt-2 text-emerald-600 hover:text-emerald-700">
                        Agregar primer curso
                    </button>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div id="add-course-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Nuevo Curso</h3>
            <button onclick="document.getElementById('add-course-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('programs.courses.store', $program) }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del curso</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-course-modal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Crear Curso
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function openAddModuleModal(courseId) {
    // Implement module modal logic
    alert('Agregar modulo al curso ' + courseId);
}

function openAddContentModal(moduleId) {
    // Implement content modal logic  
    alert('Agregar contenido al modulo ' + moduleId);
}
</script>
@endpush
@endsection
