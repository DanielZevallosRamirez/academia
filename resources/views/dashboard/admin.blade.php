@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Administrativo')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Estudiantes -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Estudiantes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_estudiantes']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-600 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    Activos
                </span>
            </div>
        </div>

        <!-- Total Profesores -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Profesores</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_profesores']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-check" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Ingresos del Mes -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ingresos del Mes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">S/ {{ number_format($stats['ingresos_mes'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
        </div>

        <!-- Pagos Pendientes -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pagos Pendientes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pagos_pendientes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
                </div>
            </div>
            @if($stats['pagos_vencidos'] > 0)
                <div class="mt-4">
                    <span class="text-red-600 text-sm flex items-center gap-1">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        {{ $stats['pagos_vencidos'] }} vencidos
                    </span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Inscripciones Recientes -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Inscripciones Recientes</h2>
                    <a href="{{ route('students.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Ver todos</a>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentEnrollments as $enrollment)
                    <div class="p-4 flex items-center gap-4">
                        <img src="{{ $enrollment->student->photo_url }}" alt="{{ $enrollment->student->name }}" 
                             class="w-10 h-10 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $enrollment->student->name }}</p>
                            <p class="text-sm text-gray-500">{{ $enrollment->program->name }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            {{ $enrollment->status === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                        <p>No hay inscripciones recientes</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagos Recientes -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Pagos Recientes</h2>
                    <a href="{{ route('payments.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Ver todos</a>
                </div>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentPayments as $payment)
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            {{ $payment->status === 'pagado' ? 'bg-green-100' : 'bg-amber-100' }}">
                            <i data-lucide="{{ $payment->status === 'pagado' ? 'check' : 'clock' }}" 
                               class="w-5 h-5 {{ $payment->status === 'pagado' ? 'text-green-600' : 'text-amber-600' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $payment->student->name }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">S/ {{ number_format($payment->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                        <p>No hay pagos recientes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Proximas Sesiones -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Proximas Sesiones de Clase</h2>
                <a href="{{ route('attendance.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">Ver calendario</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sesion</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Curso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profesor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($upcomingSessions as $session)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $session->title }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $session->course->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $session->professor->photo_url }}" class="w-6 h-6 rounded-full">
                                    <span class="text-sm text-gray-600">{{ $session->professor->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $session->session_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No hay sesiones programadas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
