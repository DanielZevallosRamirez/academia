@extends('layouts.app')

@section('title', $module->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('student.courses') }}" class="hover:text-gray-700">Mis Cursos</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('student.course', $module->course) }}" class="hover:text-gray-700">{{ $module->course->name }}</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-900">{{ $module->name }}</span>
    </nav>

    <!-- Module Header -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $module->name }}</h1>
                <p class="text-gray-600 mt-2">{{ $module->description }}</p>
                <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $module->duration_hours }} horas
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $module->contents->count() }} contenidos
                    </span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Progreso del modulo</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $progress ?? 0 }}%</p>
            </div>
        </div>
    </div>

    <!-- Contents List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Contenidos del Modulo</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($module->contents as $content)
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Content Type Icon -->
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center
                            @if($content->type == 'video') bg-red-100
                            @elseif($content->type == 'pdf') bg-blue-100
                            @elseif($content->type == 'audio') bg-purple-100
                            @else bg-gray-100 @endif">
                            @if($content->type == 'video')
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @elseif($content->type == 'pdf')
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            @elseif($content->type == 'audio')
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-medium text-gray-900">{{ $content->title }}</h3>
                                @if($content->is_free)
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs rounded-full">Gratis</span>
                                @endif
                                @if($content->isCompleted ?? false)
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs rounded-full">Completado</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ $content->description }}</p>
                            <div class="flex items-center gap-3 mt-2 text-xs text-gray-400">
                                <span class="uppercase">{{ $content->type }}</span>
                                @if($content->duration_minutes)
                                <span>{{ $content->duration_minutes }} min</span>
                                @endif
                                @if($content->file_size)
                                <span>{{ number_format($content->file_size / 1024 / 1024, 1) }} MB</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($content->type == 'video')
                        <a href="{{ route('student.content.view', $content) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            </svg>
                            Ver video
                        </a>
                        @elseif($content->type == 'pdf')
                        <a href="{{ route('student.content.view', $content) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Ver PDF
                        </a>
                        <a href="{{ route('student.content.download', $content) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                        @elseif($content->type == 'audio')
                        <a href="{{ route('student.content.view', $content) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                            </svg>
                            Escuchar
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-4 text-gray-500">Este modulo aun no tiene contenidos</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex items-center justify-between">
        @if($previousModule)
        <a href="{{ route('student.module', $previousModule) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Modulo anterior
        </a>
        @else
        <div></div>
        @endif

        @if($nextModule)
        <a href="{{ route('student.module', $nextModule) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Siguiente modulo
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @endif
    </div>
</div>
@endsection
