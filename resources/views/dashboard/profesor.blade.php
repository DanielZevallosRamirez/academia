@extends('layouts.app')

@section('title', 'Dashboard Profesor')
@section('page-title', 'Mi Dashboard')
@section('page-description', 'Bienvenido, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">
    <!-- Greeting -->
    <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-2xl p-6 text-white shadow-lg shadow-violet-200/50">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Bienvenido, {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-violet-100">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-white/20 rounded-xl backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span class="font-medium">{{ $mis_programas->count() }} programas asignados</span>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Mis Programas</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ $mis_programas->count() }}</p>
                    <p class="text-sm text-slate-500 mt-2">Programas asignados</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Sesiones Hoy</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ is_countable($sesiones_hoy) ? count($sesiones_hoy) : 0 }}</p>
                    <p class="text-sm text-emerald-600 mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Programadas
                    </p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Mis Estudiantes</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ $total_estudiantes }}</p>
                    <p class="text-sm text-slate-500 mt-2">Inscritos activos</p>
                </div>
                <div class="w-12 h-12 bg-violet-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mis Programas -->
        <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-800">Mis Programas</h2>
                <p class="text-sm text-slate-500">Programas que dicto actualmente</p>
            </div>
            <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                @forelse($mis_programas as $programa)
                    <a href="{{ route('programs.show', $programa) }}" class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors block">
                        <div class="w-12 h-12 bg-gradient-to-br from-violet-400 to-violet-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $programa->name }}</p>
                            <p class="text-xs text-slate-500">{{ $programa->courses->count() }} cursos - {{ $programa->enrollments->where('status', 'activo')->count() }} estudiantes</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $programa->status == 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ ucfirst($programa->status) }}
                        </span>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-600">Sin programas asignados</p>
                        <p class="text-xs text-slate-500 mt-1">No tienes programas asignados aun</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Proximas Sesiones -->
        <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-800">Proximas Sesiones</h2>
                <p class="text-sm text-slate-500">Clases programadas</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($proximas_sesiones as $sesion)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                        <div class="w-12 h-12 bg-slate-100 rounded-xl flex flex-col items-center justify-center flex-shrink-0">
                            <span class="text-xs text-slate-500 uppercase">{{ $sesion->session_date->format('M') }}</span>
                            <span class="text-lg font-bold text-slate-800">{{ $sesion->session_date->format('d') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $sesion->title }}</p>
                            <p class="text-xs text-slate-500">{{ $sesion->course->name ?? 'Sin curso' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-slate-800">{{ $sesion->start_time->format('H:i') ?? '' }}</p>
                            <p class="text-xs text-slate-500">hrs</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-600">Sin sesiones programadas</p>
                        <p class="text-xs text-slate-500 mt-1">No hay clases proximas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Mis Estudiantes -->
    <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Mis Estudiantes</h2>
                <p class="text-sm text-slate-500">Estudiantes inscritos en mis programas</p>
            </div>
            <span class="text-sm text-slate-500">{{ $total_estudiantes }} total</span>
        </div>
        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            @forelse($mis_estudiantes as $estudiante)
                <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-sm">{{ strtoupper(substr($estudiante->name, 0, 2)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $estudiante->name }}</p>
                        <p class="text-xs text-slate-500">{{ $estudiante->email }}</p>
                    </div>
                    <div class="text-right">
                        @php
                            $inscripcion = $estudiante->enrollments->whereIn('program_id', $mis_programas->pluck('id'))->first();
                        @endphp
                        @if($inscripcion)
                            <p class="text-xs text-slate-500 truncate max-w-32">{{ $inscripcion->program->name ?? '' }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Sin estudiantes</p>
                    <p class="text-xs text-slate-500 mt-1">No hay estudiantes inscritos en tus programas</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <a href="{{ route('attendance.index') }}" class="bg-white rounded-2xl p-5 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-violet-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <p class="font-medium text-slate-800">Tomar Asistencia</p>
            <p class="text-sm text-slate-500 mt-1">Registrar asistencia</p>
        </a>

        <a href="{{ route('programs.index') }}" class="bg-white rounded-2xl p-5 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-violet-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <p class="font-medium text-slate-800">Ver Programas</p>
            <p class="text-sm text-slate-500 mt-1">Contenido de cursos</p>
        </a>

        <a href="{{ route('profile.edit') }}" class="bg-white rounded-2xl p-5 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-violet-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <p class="font-medium text-slate-800">Mi Perfil</p>
            <p class="text-sm text-slate-500 mt-1">Configurar cuenta</p>
        </a>
    </div>
</div>
@endsection
