@extends('layouts.app')

@section('title', $student->name)
@section('page-title', 'Detalle de Estudiante')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Volver
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('students.qr', $student) }}" class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                <i data-lucide="qr-code" class="w-4 h-4"></i>
                Ver QR
            </a>
            <a href="{{ route('students.edit', $student) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                <i data-lucide="edit" class="w-4 h-4"></i>
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="text-center">
                    <img src="{{ $student->photo_url }}" alt="{{ $student->name }}" 
                         class="w-32 h-32 rounded-full mx-auto object-cover">
                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $student->name }}</h2>
                    <span class="inline-flex items-center px-3 py-1 mt-2 text-sm font-medium rounded-full 
                        {{ $student->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $student->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="flex items-center gap-3 text-sm">
                        <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                        <span class="text-gray-600">{{ $student->email }}</span>
                    </div>
                    @if($student->phone)
                        <div class="flex items-center gap-3 text-sm">
                            <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
                            <span class="text-gray-600">{{ $student->phone }}</span>
                        </div>
                    @endif
                    @if($student->dni)
                        <div class="flex items-center gap-3 text-sm">
                            <i data-lucide="credit-card" class="w-5 h-5 text-gray-400"></i>
                            <span class="text-gray-600">DNI: {{ $student->dni }}</span>
                        </div>
                    @endif
                    @if($student->address)
                        <div class="flex items-start gap-3 text-sm">
                            <i data-lucide="map-pin" class="w-5 h-5 text-gray-400 flex-shrink-0"></i>
                            <span class="text-gray-600">{{ $student->address }}</span>
                        </div>
                    @endif
                </div>

                @if($student->emergency_contact)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Contacto de Emergencia</h4>
                        <p class="text-sm text-gray-600">{{ $student->emergency_contact }}</p>
                        @if($student->emergency_phone)
                            <p class="text-sm text-gray-500">{{ $student->emergency_phone }}</p>
                        @endif
                    </div>
                @endif

                <!-- Stats -->
                <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $student->getAttendanceRate() }}%</p>
                        <p class="text-xs text-gray-500">Asistencia</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $student->getPendingPaymentsCount() }}</p>
                        <p class="text-xs text-gray-500">Pagos Pendientes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Enrollments -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Inscripciones</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($student->enrollments as $enrollment)
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $enrollment->program->name }}</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $enrollment->start_date->format('d/m/Y') }} - {{ $enrollment->end_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 text-sm font-medium rounded-full
                                    {{ $enrollment->status === 'activo' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $enrollment->status === 'completado' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $enrollment->status === 'suspendido' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $enrollment->status === 'cancelado' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </div>
                            <div class="mt-3">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-500">Progreso de pago</span>
                                    <span class="font-medium">{{ $enrollment->payment_progress }}%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $enrollment->payment_progress }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    S/ {{ number_format($enrollment->total_paid, 2) }} de S/ {{ number_format($enrollment->program->price, 2) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <p>Sin inscripciones</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Pagos Recientes</h3>
                    <a href="{{ route('payments.create', ['student_id' => $student->id]) }}" 
                       class="text-sm text-emerald-600 hover:text-emerald-700">
                        + Agregar pago
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Factura</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Monto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Vencimiento</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($student->payments->take(5) as $payment)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-mono">{{ $payment->invoice_number }}</td>
                                    <td class="px-4 py-3 text-sm">S/ {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $payment->due_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $payment->status_badge }}">
                                            {{ $payment->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Sin pagos registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Attendance -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Asistencia Reciente</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($student->attendances->take(5) as $attendance)
                        <div class="p-4 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $attendance->status_badge }}">
                                @if($attendance->status === 'presente')
                                    <i data-lucide="check" class="w-5 h-5"></i>
                                @elseif($attendance->status === 'ausente')
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                @else
                                    <i data-lucide="clock" class="w-5 h-5"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $attendance->classSession->title }}</p>
                                <p class="text-sm text-gray-500">{{ $attendance->classSession->course->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-900">{{ $attendance->classSession->session_date->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $attendance->status_label }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <p>Sin registros de asistencia</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
