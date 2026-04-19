@extends('layouts.app')

@section('title', $session->title)
@section('page-title', 'Sesion de Clase')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Volver
        </a>
        <div class="flex items-center gap-2">
            @if($session->status === 'programada')
                <form method="POST" action="{{ route('profesor.attendance.start', $session) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                        <i data-lucide="play" class="w-4 h-4"></i>
                        Iniciar Sesion
                    </button>
                </form>
            @elseif($session->status === 'en_curso')
                <a href="{{ route('attendance.scanner', $session) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                    <i data-lucide="scan-line" class="w-4 h-4"></i>
                    Abrir Escaner
                </a>
                <form method="POST" action="{{ route('profesor.attendance.end', $session) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                        <i data-lucide="square" class="w-4 h-4"></i>
                        Finalizar
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Session Info -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $session->title }}</h1>
                <p class="text-gray-500 mt-1">{{ $session->course->name }} - {{ $session->course->program->name }}</p>
                @if($session->description)
                    <p class="text-gray-600 mt-2">{{ $session->description }}</p>
                @endif
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    {{ $session->status === 'en_curso' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $session->status === 'programada' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $session->status === 'finalizada' ? 'bg-gray-100 text-gray-700' : '' }}
                    {{ $session->status === 'cancelada' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                </span>
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-gray-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Fecha</p>
                    <p class="font-medium text-gray-900">{{ $session->session_date->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-gray-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Horario</p>
                    <p class="font-medium text-gray-900">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-gray-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Profesor</p>
                    <p class="font-medium text-gray-900">{{ $session->professor->name }}</p>
                </div>
            </div>
            @if($session->location)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Ubicacion</p>
                        <p class="font-medium text-gray-900">{{ $session->location }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance Stats -->
    @php $stats = $session->getAttendanceStats(); @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500">Total</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-200 p-4 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['presente'] }}</p>
            <p class="text-sm text-green-600">Presentes</p>
        </div>
        <div class="bg-red-50 rounded-xl border border-red-200 p-4 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $stats['ausente'] }}</p>
            <p class="text-sm text-red-600">Ausentes</p>
        </div>
        <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-4 text-center">
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['tardanza'] }}</p>
            <p class="text-sm text-yellow-600">Tardanzas</p>
        </div>
        <div class="bg-blue-50 rounded-xl border border-blue-200 p-4 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['justificado'] }}</p>
            <p class="text-sm text-blue-600">Justificados</p>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Lista de Asistencia</h2>
            <span class="text-sm text-gray-500">
                Tasa de asistencia: {{ $session->getAttendanceRate() }}%
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metodo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($session->attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $attendance->student->photo_url }}" class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $attendance->student->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $attendance->student->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $attendance->status_badge }}">
                                    {{ $attendance->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $attendance->check_in_time?->format('H:i:s') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">
                                {{ $attendance->check_in_method }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    @foreach(['presente', 'ausente', 'tardanza', 'justificado'] as $status)
                                        <form method="POST" action="{{ route('profesor.attendance.update', $attendance) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" 
                                                    class="p-2 rounded-lg transition-colors
                                                        {{ $attendance->status === $status ? 'bg-emerald-100 text-emerald-700' : 'hover:bg-gray-100 text-gray-400' }}"
                                                    title="{{ ucfirst($status) }}">
                                                @if($status === 'presente')
                                                    <i data-lucide="check" class="w-4 h-4"></i>
                                                @elseif($status === 'ausente')
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                @elseif($status === 'tardanza')
                                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                                @else
                                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
