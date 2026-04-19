@extends('layouts.app')

@section('title', 'Mis Cursos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mis Cursos</h1>
        <p class="text-gray-600 mt-1">Accede a tus contenidos y materiales de estudio</p>
    </div>

    @forelse($enrollments as $enrollment)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Program Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">{{ $enrollment->program->name }}</h2>
                    <p class="text-indigo-200 mt-1">Inscrito desde {{ $enrollment->start_date->format('d/m/Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-indigo-200">Progreso general</p>
                    <p class="text-2xl font-bold">{{ $enrollment->progress ?? 0 }}%</p>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4 bg-indigo-800 rounded-full h-2">
                <div class="bg-white rounded-full h-2 transition-all" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
            </div>
        </div>

        <!-- Courses List -->
        <div class="divide-y divide-gray-200">
            @foreach($enrollment->program->courses as $course)
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-600 font-bold">{{ $course->order }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $course->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $course->description }}</p>
                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $course->duration_hours }} horas
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    {{ $course->modules->count() }} modulos
                                </span>
                                @if($course->teacher)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $course->teacher->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('student.course', $course) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                        <span>Ver contenido</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- Modules Preview -->
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($course->modules->take(4) as $module)
                    <a href="{{ route('student.module', $module) }}" class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $module->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $module->contents->count() }} contenidos</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="mt-4 text-gray-500">No tienes cursos inscritos actualmente</p>
        <p class="text-sm text-gray-400 mt-1">Contacta a administracion para inscribirte en un programa</p>
    </div>
    @endforelse
</div>
@endsection
