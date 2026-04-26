@extends('layouts.app')

@section('title', 'Control de Asistencia')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Control de Asistencia</h1>
            <p class="text-slate-500 mt-1">Gestiona las sesiones de clase y el control de asistencia</p>
        </div>
        <a href="{{ route('attendance.create-session') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-medium transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Sesion
        </a>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Fecha</label>
                <input type="date" name="date" value="{{ request('date') }}" 
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Curso</label>
                <select name="course_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Todos los cursos</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-slate-700 mb-1">Profesor</label>
                <select name="professor_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Todos los profesores</option>
                    @foreach($professors as $professor)
                        <option value="{{ $professor->id }}" {{ request('professor_id') == $professor->id ? 'selected' : '' }}>
                            {{ $professor->name }} {{ $professor->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors">
                    Filtrar
                </button>
                <a href="{{ route('attendance.index') }}" class="px-4 py-2 text-slate-500 hover:text-slate-700 transition-colors">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- Lista de Sesiones --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if($sessions->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-800 mb-1">No hay sesiones programadas</h3>
                <p class="text-slate-500 mb-4">Crea una nueva sesion de clase para comenzar a registrar asistencia</p>
                <a href="{{ route('attendance.create-session') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear primera sesion
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sesion</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Curso</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Profesor</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Fecha y Hora</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Asistencia</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                            <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($sessions as $session)
                            @php
                                $totalAttendances = $session->attendances->count();
                                $presentCount = $session->attendances->whereIn('status', ['presente', 'tardanza'])->count();
                                $attendanceRate = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">{{ $session->title }}</div>
                                    @if($session->location)
                                        <div class="text-sm text-slate-500">{{ $session->location }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-800">{{ $session->course->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ $session->course->program->name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <span class="text-emerald-600 font-medium text-xs">
                                                {{ strtoupper(substr($session->professor->name ?? 'N', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-slate-700">{{ $session->professor->name ?? 'Sin asignar' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-800">{{ $session->session_date?->format('d/m/Y') ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $session->start_time?->format('H:i') ?? '' }} - {{ $session->end_time?->format('H:i') ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden max-w-[80px]">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $attendanceRate }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-slate-700">{{ $attendanceRate }}%</span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">{{ $presentCount }}/{{ $totalAttendances }} presentes</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'programada' => 'bg-blue-100 text-blue-700',
                                            'en_curso' => 'bg-amber-100 text-amber-700',
                                            'finalizada' => 'bg-emerald-100 text-emerald-700',
                                            'cancelada' => 'bg-red-100 text-red-700',
                                        ];
                                        $statusLabels = [
                                            'programada' => 'Programada',
                                            'en_curso' => 'En curso',
                                            'finalizada' => 'Finalizada',
                                            'cancelada' => 'Cancelada',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColors[$session->status] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $statusLabels[$session->status] ?? ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('attendance.session', $session) }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Ver asistencia">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('attendance.edit-session', $session) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Editar sesion">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        @if($session->status !== 'finalizada')
                                            <a href="{{ route('attendance.scanner', $session) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Escanear QR">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginacion --}}
            @if($sessions->hasPages())
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $sessions->withQueryString()->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
