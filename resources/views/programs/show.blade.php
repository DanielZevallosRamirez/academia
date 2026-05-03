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
                    {{ $program->status === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $program->status === 'activo' ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-6 text-sm">
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
                @if($program->teacher)
                <span class="flex items-center gap-2 text-gray-600">
                    <i data-lucide="user-check" class="w-4 h-4 text-indigo-500"></i>
                    <span class="font-medium">Profesor:</span> {{ $program->teacher->name }}
                </span>
                @endif
            </div>

            <!-- Schedule & Dates Info -->
            @if($program->start_date || $program->schedule)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex flex-wrap gap-6 text-sm">
                    @if($program->start_date)
                    <div class="flex items-center gap-2 text-gray-600">
                        <i data-lucide="calendar" class="w-4 h-4 text-emerald-500"></i>
                        <span class="font-medium">Inicio:</span>
                        {{ $program->start_date->format('d/m/Y') }}
                        @if($program->end_date)
                            <span class="text-gray-400">-</span>
                            <span class="font-medium">Fin:</span>
                            {{ $program->end_date->format('d/m/Y') }}
                        @endif
                    </div>
                    @endif
                    @if($program->schedule)
                    <div class="flex items-start gap-2 text-gray-600">
                        <i data-lucide="clock-4" class="w-4 h-4 text-emerald-500 mt-0.5"></i>
                        <div>
                            <span class="font-medium">Horarios:</span>
                            @php
                                $schedules = is_array($program->schedule) ? $program->schedule : (json_decode($program->schedule, true) ?? [$program->schedule]);
                            @endphp
                            <ul class="list-disc list-inside ml-2">
                                @foreach($schedules as $schedule)
                                    <li>{{ $schedule }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
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
                    <div class="bg-gray-50 p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4 cursor-pointer flex-1" @click="open = !open">
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
                                {{ $course->status === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $course->status === 'activo' ? 'Activo' : 'Inactivo' }}
                            </span>
                            <button onclick="event.stopPropagation(); openEditCourseModal({{ $course->id }}, '{{ addslashes($course->name) }}', '{{ addslashes($course->description ?? '') }}', '{{ $course->status }}')" 
                                    class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Editar curso">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <button onclick="event.stopPropagation(); deleteCourse({{ $course->id }}, '{{ addslashes($course->name) }}')" 
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar curso">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform cursor-pointer" :class="{ 'rotate-180': open }" @click="open = !open"></i>
                        </div>
                    </div>

                    <!-- Modules -->
                    <div x-show="open" x-collapse class="border-t border-gray-200">
                        @foreach($course->modules as $module)
                            <div class="border-b border-gray-100 last:border-0" x-data="{ moduleOpen: false }">
                                <!-- Module Header -->
                                <div class="p-4 pl-16 flex items-center justify-between hover:bg-gray-50">
                                    <div class="flex items-center gap-3 cursor-pointer flex-1" @click="moduleOpen = !moduleOpen">
                                        <div class="w-8 h-8 bg-slate-100 rounded flex items-center justify-center">
                                            <span class="text-sm font-medium text-slate-600">{{ $loop->iteration }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $module->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $module->contents->count() }} contenidos</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button onclick="openEditModuleModal({{ $module->id }}, '{{ addslashes($module->name) }}', '{{ addslashes($module->description ?? '') }}')" 
                                                class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded transition-colors" title="Editar modulo">
                                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button onclick="deleteModule({{ $module->id }}, '{{ addslashes($module->name) }}')" 
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Eliminar modulo">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform cursor-pointer" :class="{ 'rotate-180': moduleOpen }" @click="moduleOpen = !moduleOpen"></i>
                                    </div>
                                </div>

                                <!-- Contents -->
                                <div x-show="moduleOpen" x-collapse class="bg-gray-50 border-t border-gray-100">
                                    @foreach($module->contents as $content)
                                        <div class="p-3 pl-24 flex items-center gap-3 hover:bg-gray-100 group">
                                            @php
                                                $contentUrl = null;
                                                if ($content->type === 'video' || $content->type === 'link') {
                                                    $contentUrl = $content->external_url;
                                                } elseif (in_array($content->type, ['pdf', 'audio']) && $content->file_path) {
                                                    $contentUrl = Storage::url($content->file_path);
                                                }
                                            @endphp
                                            
                                            @if($contentUrl)
                                            <a href="{{ $contentUrl }}" target="_blank" class="flex items-center gap-3 flex-1 cursor-pointer">
                                            @else
                                            <div class="flex items-center gap-3 flex-1 {{ $content->type === 'text' ? 'cursor-pointer' : '' }}" @if($content->type === 'text') onclick="openTextContentModal('{{ addslashes($content->title) }}', '{{ addslashes($content->content_text ?? '') }}')" @endif>
                                            @endif
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                                    {{ $content->type === 'pdf' ? 'bg-red-100 text-red-600' : '' }}
                                                    {{ $content->type === 'video' ? 'bg-blue-100 text-blue-600' : '' }}
                                                    {{ $content->type === 'audio' ? 'bg-purple-100 text-purple-600' : '' }}
                                                    {{ $content->type === 'link' ? 'bg-green-100 text-green-600' : '' }}
                                                    {{ $content->type === 'text' ? 'bg-gray-100 text-gray-600' : '' }}">
                                                    <i data-lucide="{{ $content->icon }}" class="w-4 h-4"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900 hover:text-emerald-600">{{ $content->title }}</p>
                                                    @if($content->description)
                                                        <p class="text-xs text-gray-500">{{ Str::limit($content->description, 50) }}</p>
                                                    @elseif($content->duration_minutes)
                                                        <p class="text-xs text-gray-500">{{ $content->duration_minutes }} min</p>
                                                    @endif
                                                </div>
                                            @if($contentUrl)
                                            </a>
                                            @else
                                            </div>
                                            @endif
                                            <span class="text-xs text-gray-400 uppercase">{{ $content->type }}</span>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button onclick="openEditContentModal({{ $content->id }}, '{{ addslashes($content->title) }}', '{{ $content->type }}', '{{ addslashes($content->external_url ?? '') }}', '{{ addslashes($content->description ?? '') }}', '{{ addslashes($content->content_text ?? '') }}', '{{ $content->file_path ? basename($content->file_path) : '' }}')" 
                                                        class="p-1 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded" title="Editar">
                                                    <i data-lucide="edit-2" class="w-3 h-3"></i>
                                                </button>
                                                <button onclick="deleteContent({{ $content->id }}, '{{ addslashes($content->title) }}')" 
                                                        class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Eliminar">
                                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                </button>
                                            </div>
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

<!-- Edit Course Modal -->
<div id="edit-course-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Editar Curso</h3>
            <button onclick="document.getElementById('edit-course-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit-course-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del curso</label>
                <input type="text" name="name" id="edit-course-name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                <textarea name="description" id="edit-course-description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" id="edit-course-status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('edit-course-modal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Module Modal -->
<div id="edit-module-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Editar Modulo</h3>
            <button onclick="document.getElementById('edit-module-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit-module-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del modulo</label>
                <input type="text" name="name" id="edit-module-name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                <textarea name="description" id="edit-module-description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('edit-module-modal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Content Modal -->
<div id="edit-content-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold">Editar Contenido</h3>
            <button onclick="document.getElementById('edit-content-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit-content-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titulo <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="edit-content-title" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                <select name="type" id="edit-content-type" onchange="toggleEditContentFields()" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="video">Video</option>
                    <option value="pdf">PDF</option>
                    <option value="audio">Audio</option>
                    <option value="link">Enlace</option>
                    <option value="text">Texto</option>
                </select>
            </div>
            
            <!-- Campo URL (Video / Enlace) -->
            <div id="edit-field-url" class="edit-content-field">
                <label id="edit-url-label" class="block text-sm font-medium text-gray-700 mb-1">URL del Video <span class="text-red-500">*</span></label>
                <input type="url" name="url" id="edit-content-url" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-gray-500 mt-1">YouTube, Vimeo u otra plataforma de video</p>
            </div>
            
            <!-- Campo Archivo (PDF / Audio) - para reemplazar -->
            <div id="edit-field-file" class="edit-content-field hidden">
                <label id="edit-file-label" class="block text-sm font-medium text-gray-700 mb-1">Reemplazar Archivo</label>
                <div id="edit-current-file" class="mb-2 p-3 bg-gray-50 rounded-lg flex items-center gap-2 text-sm text-gray-600">
                    <i data-lucide="file" class="w-4 h-4 text-emerald-500"></i>
                    <span id="edit-current-file-name">Archivo actual</span>
                </div>
                <input type="file" name="file" id="edit-content-file" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-gray-500 mt-1">Deja vacio para mantener el archivo actual</p>
            </div>
            
            <!-- Campo Texto -->
            <div id="edit-field-text" class="edit-content-field hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido <span class="text-red-500">*</span></label>
                <textarea name="content_text" id="edit-content-text" rows="6" placeholder="Escribe el contenido aqui..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            
            <!-- Campo Descripcion (Video, PDF, Audio) -->
            <div id="edit-field-description" class="edit-content-field">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                <textarea name="description" id="edit-content-description" rows="3" placeholder="Descripcion opcional del contenido..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('edit-content-modal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Module Modal -->
<div id="add-module-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Nuevo Modulo</h3>
            <button onclick="document.getElementById('add-module-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="add-module-form" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del modulo</label>
                <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-module-modal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Crear Modulo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Content Modal -->
<div id="add-content-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <h3 class="text-lg font-semibold">Nuevo Contenido</h3>
            <button onclick="document.getElementById('add-content-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="add-content-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titulo <span class="text-red-500">*</span></label>
                <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                <select name="type" id="content-type-select" onchange="toggleContentFields()" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="video">Video</option>
                    <option value="pdf">PDF</option>
                    <option value="audio">Audio</option>
                    <option value="link">Enlace</option>
                    <option value="text">Texto</option>
                </select>
            </div>
            
            <!-- Campo URL (Video / Enlace) -->
            <div id="field-url" class="content-field">
                <label class="block text-sm font-medium text-gray-700 mb-1">URL del Video <span class="text-red-500">*</span></label>
                <input type="url" name="external_url" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-gray-500 mt-1">YouTube, Vimeo u otra plataforma de video</p>
            </div>
            
            <!-- Campo Archivos PDF -->
            <div id="field-pdf" class="content-field hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivos PDF <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-emerald-400 transition-colors">
                    <input type="file" name="files[]" multiple accept=".pdf" class="hidden" id="pdf-files-input" onchange="showSelectedFiles(this, 'pdf-files-list')">
                    <label for="pdf-files-input" class="cursor-pointer">
                        <i data-lucide="file-text" class="w-8 h-8 mx-auto text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Click para seleccionar archivos PDF</p>
                        <p class="text-xs text-gray-500">Puedes seleccionar varios archivos</p>
                    </label>
                </div>
                <div id="pdf-files-list" class="mt-2 space-y-1"></div>
            </div>
            
            <!-- Campo Archivos Audio -->
            <div id="field-audio" class="content-field hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivos de Audio <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-emerald-400 transition-colors">
                    <input type="file" name="files[]" multiple accept=".mp3,.wav,.ogg" class="hidden" id="audio-files-input" onchange="showSelectedFiles(this, 'audio-files-list')">
                    <label for="audio-files-input" class="cursor-pointer">
                        <i data-lucide="music" class="w-8 h-8 mx-auto text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Click para seleccionar archivos MP3</p>
                        <p class="text-xs text-gray-500">Formatos: MP3, WAV, OGG</p>
                    </label>
                </div>
                <div id="audio-files-list" class="mt-2 space-y-1"></div>
            </div>
            
            <!-- Campo Texto -->
            <div id="field-text" class="content-field hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido <span class="text-red-500">*</span></label>
                <textarea name="content_text" rows="6" placeholder="Escribe el contenido aqui..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            
            <!-- Campo Descripcion (Video, PDF, Audio) -->
            <div id="field-description" class="content-field">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                <textarea name="description" rows="3" placeholder="Descripcion opcional del contenido..." class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-content-modal').classList.add('hidden')" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Crear Contenido
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Forms (hidden) -->
<form id="delete-course-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
<form id="delete-module-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
<!-- Text Content View Modal -->
<div id="text-content-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl w-full max-w-2xl mx-4 max-h-[80vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h3 id="text-content-title" class="text-lg font-semibold">Contenido</h3>
            <button onclick="document.getElementById('text-content-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="text-content-body" class="prose prose-sm max-w-none text-gray-700 whitespace-pre-wrap"></div>
        </div>
        <div class="p-4 border-t border-gray-200 flex justify-end">
            <button onclick="document.getElementById('text-content-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Cerrar
            </button>
        </div>
    </div>
</div>

<form id="delete-content-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Course functions
function openEditCourseModal(courseId, name, description, status) {
    document.getElementById('edit-course-form').action = '/courses/' + courseId;
    document.getElementById('edit-course-name').value = name;
    document.getElementById('edit-course-description').value = description;
    document.getElementById('edit-course-status').value = status;
    document.getElementById('edit-course-modal').classList.remove('hidden');
    lucide.createIcons();
}

function deleteCourse(courseId, name) {
    Swal.fire({
        title: 'Eliminar Curso',
        html: `<p class="text-gray-600">Estas seguro de eliminar el curso <strong>"${name}"</strong>?</p><p class="text-red-500 text-sm mt-2">Esta accion eliminara todos los modulos y contenidos asociados.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-course-form');
            form.action = '/courses/' + courseId;
            form.submit();
        }
    });
}

// Module functions
function openAddModuleModal(courseId) {
    document.getElementById('add-module-form').action = '/courses/' + courseId + '/modules';
    document.getElementById('add-module-modal').classList.remove('hidden');
    lucide.createIcons();
}

function openEditModuleModal(moduleId, name, description) {
    document.getElementById('edit-module-form').action = '/modules/' + moduleId;
    document.getElementById('edit-module-name').value = name;
    document.getElementById('edit-module-description').value = description;
    document.getElementById('edit-module-modal').classList.remove('hidden');
    lucide.createIcons();
}

function deleteModule(moduleId, name) {
    Swal.fire({
        title: 'Eliminar Modulo',
        html: `<p class="text-gray-600">Estas seguro de eliminar el modulo <strong>"${name}"</strong>?</p><p class="text-red-500 text-sm mt-2">Esta accion eliminara todos los contenidos asociados.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-module-form');
            form.action = '/modules/' + moduleId;
            form.submit();
        }
    });
}

// Content functions
function openAddContentModal(moduleId) {
    document.getElementById('add-content-form').action = '/modules/' + moduleId + '/contents';
    document.getElementById('add-content-form').reset();
    document.getElementById('content-type-select').value = 'video';
    toggleContentFields();
    document.getElementById('add-content-modal').classList.remove('hidden');
    lucide.createIcons();
}

function toggleContentFields() {
    const type = document.getElementById('content-type-select').value;
    
    // Ocultar todos los campos
    document.querySelectorAll('.content-field').forEach(el => el.classList.add('hidden'));
    
    // Limpiar inputs de archivo
    const pdfInput = document.getElementById('pdf-files-input');
    const audioInput = document.getElementById('audio-files-input');
    if (pdfInput) pdfInput.value = '';
    if (audioInput) audioInput.value = '';
    document.getElementById('pdf-files-list').innerHTML = '';
    document.getElementById('audio-files-list').innerHTML = '';
    
    // Mostrar campos según el tipo
    switch(type) {
        case 'video':
            document.getElementById('field-url').classList.remove('hidden');
            document.getElementById('field-description').classList.remove('hidden');
            document.querySelector('#field-url label').innerHTML = 'URL del Video <span class="text-red-500">*</span>';
            break;
        case 'pdf':
            document.getElementById('field-pdf').classList.remove('hidden');
            document.getElementById('field-description').classList.remove('hidden');
            break;
        case 'audio':
            document.getElementById('field-audio').classList.remove('hidden');
            document.getElementById('field-description').classList.remove('hidden');
            break;
        case 'link':
            document.getElementById('field-url').classList.remove('hidden');
            document.querySelector('#field-url label').innerHTML = 'URL del Enlace <span class="text-red-500">*</span>';
            break;
        case 'text':
            document.getElementById('field-text').classList.remove('hidden');
            break;
    }
    
    lucide.createIcons();
}

function showSelectedFiles(input, listId) {
    const listEl = document.getElementById(listId);
    listEl.innerHTML = '';
    
    if (input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded';
            div.innerHTML = `
                <i data-lucide="file" class="w-4 h-4 text-emerald-500"></i>
                <span class="truncate flex-1">${file.name}</span>
                <span class="text-xs text-gray-400">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
            `;
            listEl.appendChild(div);
        });
        lucide.createIcons();
    }
}

function openEditContentModal(contentId, title, type, url, description, contentText, fileName) {
    document.getElementById('edit-content-form').action = '/contents/' + contentId;
    document.getElementById('edit-content-title').value = title;
    document.getElementById('edit-content-type').value = type;
    document.getElementById('edit-content-url').value = url || '';
    document.getElementById('edit-content-description').value = description || '';
    document.getElementById('edit-content-text').value = contentText || '';
    
    // Mostrar nombre del archivo actual si existe
    if (fileName) {
        document.getElementById('edit-current-file-name').textContent = fileName;
        document.getElementById('edit-current-file').classList.remove('hidden');
    } else {
        document.getElementById('edit-current-file').classList.add('hidden');
    }
    
    toggleEditContentFields();
    document.getElementById('edit-content-modal').classList.remove('hidden');
    lucide.createIcons();
}

function openTextContentModal(title, text) {
    document.getElementById('text-content-title').textContent = title;
    document.getElementById('text-content-body').textContent = text;
    document.getElementById('text-content-modal').classList.remove('hidden');
    lucide.createIcons();
}

function toggleEditContentFields() {
    const type = document.getElementById('edit-content-type').value;
    
    // Ocultar todos los campos
    document.querySelectorAll('.edit-content-field').forEach(el => el.classList.add('hidden'));
    
    // Mostrar campos según el tipo
    switch(type) {
        case 'video':
            document.getElementById('edit-field-url').classList.remove('hidden');
            document.getElementById('edit-field-description').classList.remove('hidden');
            document.getElementById('edit-url-label').innerHTML = 'URL del Video <span class="text-red-500">*</span>';
            break;
        case 'pdf':
            document.getElementById('edit-field-file').classList.remove('hidden');
            document.getElementById('edit-field-description').classList.remove('hidden');
            document.getElementById('edit-file-label').textContent = 'Reemplazar PDF';
            document.getElementById('edit-content-file').accept = '.pdf';
            break;
        case 'audio':
            document.getElementById('edit-field-file').classList.remove('hidden');
            document.getElementById('edit-field-description').classList.remove('hidden');
            document.getElementById('edit-file-label').textContent = 'Reemplazar Audio';
            document.getElementById('edit-content-file').accept = '.mp3,.wav,.ogg';
            break;
        case 'link':
            document.getElementById('edit-field-url').classList.remove('hidden');
            document.getElementById('edit-url-label').innerHTML = 'URL del Enlace <span class="text-red-500">*</span>';
            break;
        case 'text':
            document.getElementById('edit-field-text').classList.remove('hidden');
            break;
    }
    
    lucide.createIcons();
}

function deleteContent(contentId, title) {
    Swal.fire({
        title: 'Eliminar Contenido',
        html: `<p class="text-gray-600">Estas seguro de eliminar el contenido <strong>"${title}"</strong>?</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-content-form');
            form.action = '/contents/' + contentId;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
