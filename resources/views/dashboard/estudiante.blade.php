@extends('layouts.app')

@section('title', 'Mi Dashboard')
@section('page-title', 'Mi Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Greeting & Quick Stats -->
    <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Hola, {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-slate-300">Continua tu aprendizaje donde lo dejaste</p>
            </div>
            @if($enrollment)
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-lg">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                    <span>{{ $stats['dias_restantes'] }} dias restantes</span>
                </div>
            @endif
        </div>
    </div>

    @if(!$enrollment)
        <!-- No enrollment message -->
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-8 text-center">
            <i data-lucide="alert-circle" class="w-16 h-16 mx-auto text-amber-500 mb-4"></i>
            <h2 class="text-xl font-semibold text-gray-900">No tienes una inscripcion activa</h2>
            <p class="mt-2 text-gray-600">Contacta con administracion para inscribirte en un programa.</p>
        </div>
    @else
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Asistencia</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['asistencia_rate'] }}%</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                        <i data-lucide="calendar-check" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $stats['asistencia_rate'] }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pagos Pendientes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['pagos_pendientes'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full {{ $stats['pagos_pendientes'] > 0 ? 'bg-amber-100' : 'bg-emerald-100' }} flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-6 h-6 {{ $stats['pagos_pendientes'] > 0 ? 'text-amber-600' : 'text-emerald-600' }}"></i>
                    </div>
                </div>
                <a href="{{ route('estudiante.my-payments') }}" class="mt-4 text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    Ver mis pagos
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Mi Programa</p>
                        <p class="text-lg font-bold text-gray-900 mt-1 truncate">{{ $program->name }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
                <a href="{{ route('estudiante.my-program') }}" class="mt-4 text-sm text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    Continuar aprendiendo
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- Course Progress -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Mi Progreso por Curso</h2>
            </div>
            <div class="p-6 space-y-4">
                @foreach($program->courses as $course)
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="book-open" class="w-5 h-5 text-slate-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $course->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $course->modules->count() }} modulos</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold {{ $courseProgress[$course->id] >= 100 ? 'text-emerald-600' : 'text-gray-600' }}">
                                {{ $courseProgress[$course->id] }}%
                            </span>
                        </div>
                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-300" 
                                 style="width: {{ $courseProgress[$course->id] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Attendance -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Asistencia Reciente</h2>
                    <a href="{{ route('estudiante.my-attendance') }}" class="text-sm text-emerald-600">Ver todo</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentAttendances as $attendance)
                        <div class="p-4 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $attendance->status_badge }}">
                                @if($attendance->status === 'presente')
                                    <i data-lucide="check" class="w-5 h-5"></i>
                                @elseif($attendance->status === 'ausente')
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                @elseif($attendance->status === 'tardanza')
                                    <i data-lucide="clock" class="w-5 h-5"></i>
                                @else
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $attendance->classSession->title }}</p>
                                <p class="text-sm text-gray-500">{{ $attendance->classSession->session_date->format('d/m/Y') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $attendance->status_badge }}">
                                {{ $attendance->status_label }}
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <p>No hay registros de asistencia</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pending Payments -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Pagos Pendientes</h2>
                    <a href="{{ route('estudiante.my-payments') }}" class="text-sm text-emerald-600">Ver todo</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($pendingPayments as $payment)
                        <div class="p-4 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                {{ $payment->isOverdue() ? 'bg-red-100' : 'bg-amber-100' }}">
                                <i data-lucide="{{ $payment->isOverdue() ? 'alert-triangle' : 'clock' }}" 
                                   class="w-5 h-5 {{ $payment->isOverdue() ? 'text-red-600' : 'text-amber-600' }}"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $payment->invoice_number }}</p>
                                <p class="text-sm text-gray-500">Vence: {{ $payment->due_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">S/ {{ number_format($payment->amount, 2) }}</p>
                                @if($payment->isOverdue())
                                    <span class="text-xs text-red-600">Vencido</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <i data-lucide="check-circle" class="w-12 h-12 mx-auto text-emerald-300 mb-2"></i>
                            <p>No tienes pagos pendientes</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rapidas</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('estudiante.my-program') }}" 
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-colors">
                    <i data-lucide="play-circle" class="w-8 h-8 text-emerald-600"></i>
                    <span class="text-sm font-medium text-gray-700">Continuar Curso</span>
                </a>
                <a href="{{ route('estudiante.my-qr') }}" 
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-colors">
                    <i data-lucide="qr-code" class="w-8 h-8 text-emerald-600"></i>
                    <span class="text-sm font-medium text-gray-700">Mi Codigo QR</span>
                </a>
                <a href="{{ route('estudiante.my-attendance') }}" 
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-colors">
                    <i data-lucide="calendar-check" class="w-8 h-8 text-emerald-600"></i>
                    <span class="text-sm font-medium text-gray-700">Mi Asistencia</span>
                </a>
                <a href="{{ route('estudiante.my-payments') }}" 
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition-colors">
                    <i data-lucide="receipt" class="w-8 h-8 text-emerald-600"></i>
                    <span class="text-sm font-medium text-gray-700">Mis Pagos</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
