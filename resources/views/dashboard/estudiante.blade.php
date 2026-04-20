@extends('layouts.app')

@section('title', 'Mi Dashboard')
@section('page-title', 'Mi Dashboard')
@section('page-description', 'Bienvenido, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">
    <!-- Greeting -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Hola, {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-slate-300">Continua tu aprendizaje donde lo dejaste</p>
            </div>
            @if(isset($mis_inscripciones) && $mis_inscripciones->count() > 0)
            <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="font-medium">{{ $mis_inscripciones->count() }} programa(s) activo(s)</span>
            </div>
            @endif
        </div>
    </div>

    @if(!isset($mis_inscripciones) || $mis_inscripciones->count() === 0)
        <!-- No enrollment message -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
            <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-slate-800">No tienes una inscripcion activa</h2>
            <p class="mt-2 text-slate-600">Contacta con administracion para inscribirte en un programa.</p>
        </div>
    @else
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Mis Programas</p>
                        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $mis_inscripciones->count() }}</p>
                        <p class="text-sm text-emerald-600 mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Activos
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Pagos Pendientes</p>
                        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $mis_pagos->where('status', 'pendiente')->count() }}</p>
                        @if($pago_pendiente)
                        <p class="text-sm text-amber-600 mt-2">Proximo: {{ \Carbon\Carbon::parse($pago_pendiente->due_date)->format('d/m/Y') }}</p>
                        @else
                        <p class="text-sm text-emerald-600 mt-2">Al dia</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 {{ $pago_pendiente ? 'bg-amber-100' : 'bg-emerald-100' }} rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 {{ $pago_pendiente ? 'text-amber-600' : 'text-emerald-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Contenidos Completados</p>
                        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $progreso_contenidos }}</p>
                        <p class="text-sm text-slate-500 mt-2">Lecciones vistas</p>
                    </div>
                    <div class="w-12 h-12 bg-violet-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Mis Programas -->
            <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Mis Programas</h2>
                        <p class="text-sm text-slate-500">Programas en los que estas inscrito</p>
                    </div>
                    <a href="{{ route('estudiante.my-program') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Ver todos</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($mis_inscripciones as $inscripcion)
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $inscripcion->program->name ?? 'Sin nombre' }}</p>
                                <p class="text-xs text-slate-500">{{ $inscripcion->program->courses->count() ?? 0 }} cursos</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $inscripcion->status === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($inscripcion->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagos Recientes -->
            <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Mis Pagos</h2>
                        <p class="text-sm text-slate-500">Historial de pagos</p>
                    </div>
                    <a href="{{ route('estudiante.my-payments') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Ver todos</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($mis_pagos->take(5) as $pago)
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $pago->status === 'pagado' ? 'bg-emerald-100' : 'bg-amber-100' }}">
                                @if($pago->status === 'pagado')
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800">Cuota {{ $pago->number ?? '' }}</p>
                                <p class="text-xs text-slate-500">Vence: {{ $pago->due_date ? \Carbon\Carbon::parse($pago->due_date)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-800">S/ {{ number_format($pago->amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $pago->status === 'pagado' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($pago->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-600">Sin pagos registrados</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('estudiante.my-program') }}" class="bg-white rounded-2xl p-5 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="font-medium text-slate-800">Continuar Curso</p>
                <p class="text-sm text-slate-500 mt-1">Ver contenido</p>
            </a>

            <a href="{{ route('estudiante.my-qr') }}" class="bg-white rounded-2xl p-5 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <p class="font-medium text-slate-800">Mi Codigo QR</p>
                <p class="text-sm text-slate-500 mt-1">Para asistencia</p>
            </a>

            <a href="{{ route('estudiante.my-attendance') }}" class="bg-white rounded-2xl p-5 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <p class="font-medium text-slate-800">Mi Asistencia</p>
                <p class="text-sm text-slate-500 mt-1">Ver historial</p>
            </a>

            <a href="{{ route('estudiante.my-payments') }}" class="bg-white rounded-2xl p-5 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <p class="font-medium text-slate-800">Mis Pagos</p>
                <p class="text-sm text-slate-500 mt-1">Estado de cuenta</p>
            </a>
        </div>
    @endif
</div>
@endsection
