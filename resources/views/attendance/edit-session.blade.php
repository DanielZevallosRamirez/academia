@extends('layouts.app')

@section('title', 'Editar Sesion de Clase')

@section('content')
<div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-emerald-600 transition-colors mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Asistencia
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Editar Sesion de Clase</h1>
        <p class="text-slate-500 mt-1">Modifica los datos de la sesion "{{ $session->title }}"</p>
    </div>

    {{-- Formulario --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('attendance.update-session', $session) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Curso --}}
            <div>
                <label for="course_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Curso <span class="text-red-500">*</span>
                </label>
                <select name="course_id" id="course_id" required
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('course_id') border-red-500 @enderror">
                    <option value="">Seleccionar curso...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $session->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} - {{ $course->program->name ?? 'Sin programa' }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Profesor --}}
            <div>
                <label for="professor_id" class="block text-sm font-medium text-slate-700 mb-2">
                    Profesor <span class="text-red-500">*</span>
                </label>
                <select name="professor_id" id="professor_id" required
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('professor_id') border-red-500 @enderror">
                    <option value="">Seleccionar profesor...</option>
                    @foreach($professors as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('professor_id', $session->professor_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }} {{ $teacher->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('professor_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Titulo de la sesion --}}
            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 mb-2">
                    Titulo de la Sesion <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $session->title) }}" placeholder="Ej: Clase 1 - Introduccion" required
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha y Hora --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="session_date" class="block text-sm font-medium text-slate-700 mb-2">
                        Fecha <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="session_date" id="session_date" value="{{ old('session_date', $session->session_date?->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('session_date') border-red-500 @enderror">
                    @error('session_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-slate-700 mb-2">
                        Hora de Inicio <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $session->start_time?->format('H:i')) }}" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('start_time') border-red-500 @enderror">
                    @error('start_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Hora de Fin y Ubicacion --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="end_time" class="block text-sm font-medium text-slate-700 mb-2">
                        Hora de Fin <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $session->end_time?->format('H:i')) }}" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('end_time') border-red-500 @enderror">
                    @error('end_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-slate-700 mb-2">
                        Ubicacion / Aula
                    </label>
                    <input type="text" name="location" id="location" value="{{ old('location', $session->location) }}" placeholder="Ej: Aula 101"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Estado --}}
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700 mb-2">
                    Estado <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors @error('status') border-red-500 @enderror">
                    <option value="programada" {{ old('status', $session->status) == 'programada' ? 'selected' : '' }}>Programada</option>
                    <option value="en_curso" {{ old('status', $session->status) == 'en_curso' ? 'selected' : '' }}>En curso</option>
                    <option value="finalizada" {{ old('status', $session->status) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    <option value="cancelada" {{ old('status', $session->status) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descripcion --}}
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Descripcion / Notas
                </label>
                <textarea name="description" id="description" rows="3" placeholder="Notas adicionales sobre la sesion..."
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none @error('description') border-red-500 @enderror">{{ old('description', $session->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('attendance.index') }}" class="px-6 py-3 text-slate-600 hover:text-slate-800 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-xl transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
