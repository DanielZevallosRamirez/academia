@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Bienvenido de vuelta, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <!-- Total Estudiantes -->
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Estudiantes</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ number_format($stats['total_estudiantes'] ?? 0) }}</p>
                    <p class="text-sm text-emerald-600 mt-2 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        Activos
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Profesores -->
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Profesores</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ number_format($stats['total_profesores'] ?? 0) }}</p>
                    <p class="text-sm text-slate-500 mt-2">Docentes activos</p>
                </div>
                <div class="w-12 h-12 bg-violet-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Programas Activos -->
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-slate-200/50 border border-slate-200/50">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Programas Activos</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ number_format($stats['total_programas'] ?? 0) }}</p>
                    <p class="text-sm text-slate-500 mt-2">En curso</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ingresos del Mes -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 shadow-sm shadow-emerald-200/50">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-emerald-100">Ingresos del Mes</p>
                    <p class="text-3xl font-bold text-white mt-2">S/ {{ number_format($stats['ingresos_mes'] ?? 0, 2) }}</p>
                    <p class="text-sm text-emerald-100 mt-2">{{ now()->format('F Y') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('students.create') }}" class="bg-white rounded-2xl p-4 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-800">Nuevo Estudiante</p>
            <p class="text-xs text-slate-500 mt-0.5">Registrar alumno</p>
        </a>

        <a href="{{ route('payments.create') }}" class="bg-white rounded-2xl p-4 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-800">Registrar Pago</p>
            <p class="text-xs text-slate-500 mt-0.5">Nuevo cobro</p>
        </a>

        <a href="{{ route('attendance.index') }}" class="bg-white rounded-2xl p-4 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-800">Asistencia</p>
            <p class="text-xs text-slate-500 mt-0.5">Control de clases</p>
        </a>

        <a href="{{ route('programs.index') }}" class="bg-white rounded-2xl p-4 shadow-sm shadow-slate-200/50 border border-slate-200/50 hover:border-emerald-300 hover:shadow-md transition-all group">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-slate-800">Programas</p>
            <p class="text-xs text-slate-500 mt-0.5">Gestionar cursos</p>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pagos Pendientes -->
        <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Pagos Pendientes</h2>
                    <p class="text-sm text-slate-500">Proximos vencimientos</p>
                </div>
                <a href="{{ route('payments.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Ver todos</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($pagos_pendientes ?? [] as $pago)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">
                                {{ $pago->enrollment->student->name ?? 'N/A' }} {{ $pago->enrollment->student->last_name ?? '' }}
                            </p>
                            <p class="text-xs text-slate-500">Vence: {{ $pago->due_date ? \Carbon\Carbon::parse($pago->due_date)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">S/ {{ number_format($pago->amount, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                Pendiente
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-600">Sin pagos pendientes</p>
                        <p class="text-xs text-slate-500 mt-1">Todos los pagos estan al dia</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Estudiantes Recientes -->
        <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Estudiantes Recientes</h2>
                    <p class="text-sm text-slate-500">Ultimos registrados</p>
                </div>
                <a href="{{ route('students.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Ver todos</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($estudiantes_recientes ?? [] as $estudiante)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-semibold text-sm">{{ strtoupper(substr($estudiante->name, 0, 1)) }}{{ strtoupper(substr($estudiante->last_name ?? '', 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $estudiante->name }} {{ $estudiante->last_name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $estudiante->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $estudiante->status === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($estudiante->status) }}
                            </span>
                            <p class="text-xs text-slate-500 mt-1">{{ $estudiante->created_at->diffForHumans() }}</p>
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
                        <p class="text-xs text-slate-500 mt-1">No hay estudiantes registrados aun</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Inscripciones por Mes -->
    @if(isset($inscripciones_por_mes) && $inscripciones_por_mes->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm shadow-slate-200/50 border border-slate-200/50 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-base font-semibold text-slate-800">Inscripciones por Mes</h2>
            <p class="text-sm text-slate-500">Resumen del ano {{ now()->year }}</p>
        </div>
        <div class="p-6">
            <div class="flex items-end gap-2 h-40">
                @php
                    $maxInscripciones = $inscripciones_por_mes->max('total') ?: 1;
                    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                @endphp
                @foreach($inscripciones_por_mes as $inscripcion)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-emerald-100 rounded-t-lg transition-all hover:bg-emerald-200" 
                             style="height: {{ ($inscripcion->total / $maxInscripciones) * 100 }}%">
                        </div>
                        <span class="text-xs text-slate-500">{{ $meses[$inscripcion->mes - 1] ?? '' }}</span>
                        <span class="text-xs font-medium text-slate-700">{{ $inscripcion->total }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
