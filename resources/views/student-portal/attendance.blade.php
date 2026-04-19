@extends('layouts.app')

@section('title', 'Mi Asistencia')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mi Asistencia</h1>
            <p class="text-gray-600 mt-1">Consulta tu historial de asistencia a clases</p>
        </div>
        <a href="{{ route('student.my-qr') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
            Ver mi QR
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Asistencias</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['presente'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tardanzas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['tardanza'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Faltas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['ausente'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Porcentaje</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['percentage'] ?? 0 }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form action="{{ route('student.attendance') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <select name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Attendance List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sesion</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora registro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-medium text-gray-900">{{ $attendance->classSession->date->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $attendance->classSession->date->translatedFormat('l') }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $attendance->classSession->course->name }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $attendance->classSession->title ?? 'Clase regular' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $attendance->classSession->start_time }} - {{ $attendance->classSession->end_time }}
                            </p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($attendance->check_in_time)
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($attendance->status == 'presente') bg-emerald-100 text-emerald-800
                                @elseif($attendance->status == 'tardanza') bg-amber-100 text-amber-800
                                @elseif($attendance->status == 'ausente') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-4 text-gray-500">No hay registros de asistencia</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
