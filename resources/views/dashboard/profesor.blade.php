@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Mi Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Greeting -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-6 text-white">
        <h1 class="text-2xl font-bold">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-emerald-100">{{ now()->format('l, d \d\e F \d\e Y') }}</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="book-open" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Mis Cursos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_cursos'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Sesiones Hoy</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['sesiones_hoy'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Sesiones este Mes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['sesiones_mes'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Sessions -->
    @if($todaySessions->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="calendar-check" class="w-5 h-5 text-emerald-600"></i>
                    Sesiones de Hoy
                </h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($todaySessions as $session)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="text-center min-w-[60px]">
                                    <p class="text-lg font-bold text-gray-900">{{ $session->start_time->format('H:i') }}</p>
                                    <p class="text-xs text-gray-500">{{ $session->end_time->format('H:i') }}</p>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $session->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $session->course->name }} - {{ $session->course->program->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 text-sm rounded-full
                                    {{ $session->status === 'en_curso' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $session->status === 'programada' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $session->status === 'finalizada' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                </span>
                                <a href="{{ route('attendance.session', $session) }}" 
                                   class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                    <i data-lucide="scan-line" class="w-4 h-4 inline mr-1"></i>
                                    Asistencia
                                </a>
                            </div>
                        </div>
                        
                        @php $stats = $session->getAttendanceStats(); @endphp
                        @if($stats['total'] > 0)
                            <div class="mt-3 flex items-center gap-4 text-sm">
                                <span class="text-green-600">{{ $stats['presente'] }} presentes</span>
                                <span class="text-red-600">{{ $stats['ausente'] }} ausentes</span>
                                <span class="text-yellow-600">{{ $stats['tardanza'] }} tardanzas</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Sessions -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Proximas Sesiones</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($upcomingSessions as $session)
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-xl flex flex-col items-center justify-center">
                            <span class="text-xs text-gray-500">{{ $session->session_date->format('M') }}</span>
                            <span class="text-lg font-bold text-gray-900">{{ $session->session_date->format('d') }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $session->title }}</p>
                            <p class="text-sm text-gray-500">{{ $session->course->name }}</p>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            {{ $session->start_time->format('H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                        <p>No hay sesiones programadas</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- My Courses -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Mis Cursos</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($courses as $course)
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="book-open" class="w-6 h-6 text-emerald-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $course->name }}</p>
                            <p class="text-sm text-gray-500">{{ $course->program->name }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">
                            {{ $course->modules->count() }} modulos
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i data-lucide="book-x" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                        <p>No tienes cursos asignados</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
