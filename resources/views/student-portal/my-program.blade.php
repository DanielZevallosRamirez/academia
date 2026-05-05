@extends('layouts.app')

@section('title', 'Mi Programa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mi Programa</h1>
        <p class="text-gray-600 mt-1">Accede a tus cursos, modulos y contenidos de estudio</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Program Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">{{ $program->name }}</h2>
                    <p class="text-indigo-200 mt-1">Inscrito desde {{ $enrollment->start_date ? $enrollment->start_date->format('d/m/Y') : $enrollment->created_at->format('d/m/Y') }}</p>
                    @if($program->teacher)
                    <p class="text-indigo-200 text-sm mt-1">Profesor: {{ $program->teacher->name }}</p>
                    @endif
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

        <!-- Program Info -->
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $program->courses->count() }}</p>
                    <p class="text-sm text-gray-500">Cursos</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $program->duration_months ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Meses</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600">{{ $program->total_hours ?? '-' }}</p>
                    <p class="text-sm text-gray-500">Horas</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-indigo-600">
                        @php
                            $totalContents = 0;
                            foreach($program->courses as $course) {
                                foreach($course->modules as $module) {
                                    $totalContents += $module->contents->count();
                                }
                            }
                        @endphp
                        {{ $totalContents }}
                    </p>
                    <p class="text-sm text-gray-500">Contenidos</p>
                </div>
            </div>
        </div>

        <!-- Courses List -->
        <div class="divide-y divide-gray-200">
            @foreach($program->courses as $course)
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-indigo-600 font-bold">{{ $course->order ?? $loop->iteration }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $course->name }}</h3>
                            @if($course->description)
                            <p class="text-sm text-gray-500 mt-1">{{ $course->description }}</p>
                            @endif
                            <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    {{ $course->modules->count() }} modulos
                                </span>
                                @if($course->duration_hours)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $course->duration_hours }} horas
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modules List -->
                @if($course->modules->count() > 0)
                <div class="mt-4 space-y-2">
                    @foreach($course->modules as $module)
                    <div x-data="{ open: false }" class="border border-gray-100 rounded-lg overflow-hidden">
                        <button @click="open = !open" type="button"
                                class="w-full p-4 bg-gray-50 hover:bg-gray-100 transition-colors flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-medium text-sm">{{ $module->order ?? $loop->iteration }}</span>
                                </div>
                                <div class="text-left">
                                    <p class="font-medium text-gray-900">{{ $module->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $module->contents->count() }} contenidos</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $progress = $moduleProgress[$module->id] ?? 0;
                                @endphp
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-sm font-medium {{ $progress == 100 ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ round($progress) }}%
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>
                        <!-- Contents -->
                        <div x-show="open" x-collapse class="border-t border-gray-100">
                            @forelse($module->contents as $content)
                            @php $isCompleted = $content->isCompletedBy(auth()->user()); @endphp
                            <a href="{{ route('estudiante.content.view', $content) }}" 
                               class="flex items-center gap-3 p-3 pl-16 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0
                                    {{ $isCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                    @if($isCompleted)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-700">{{ $content->title }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $content->type }}</p>
                                </div>
                                @if($content->duration_minutes)
                                <span class="text-xs text-gray-400">{{ $content->duration_minutes }} min</span>
                                @endif
                            </a>
                            @empty
                            <p class="p-3 pl-16 text-sm text-gray-500">Sin contenidos disponibles</p>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="mt-4 p-4 bg-gray-50 rounded-lg text-center text-gray-500 text-sm">
                    Este curso aun no tiene modulos disponibles
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
