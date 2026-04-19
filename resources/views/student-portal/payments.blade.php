@extends('layouts.app')

@section('title', 'Mis Pagos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mis Pagos</h1>
            <p class="text-gray-600 mt-1">Consulta el estado de tus pagos y cuotas</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total pagado</p>
                    <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($totalPaid ?? 0, 2) }}</p>
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
                    <p class="text-sm text-gray-600">Pendiente por pagar</p>
                    <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($totalPending ?? 0, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Proxima cuota</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $nextPayment ? $nextPayment->due_date->format('d/m/Y') : 'Sin cuotas' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments Alert -->
    @if($pendingPayments->count() > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h3 class="font-semibold text-amber-800">Tienes pagos pendientes</h3>
                <p class="text-amber-700 text-sm mt-1">Tienes {{ $pendingPayments->count() }} pago(s) pendiente(s). Revisa las fechas de vencimiento para evitar recargos.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Payments List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Historial de Pagos</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($payments as $payment)
            <div class="p-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center
                            @if($payment->status == 'pagado') bg-emerald-100
                            @elseif($payment->status == 'pendiente') bg-amber-100
                            @elseif($payment->status == 'vencido') bg-red-100
                            @else bg-blue-100 @endif">
                            @if($payment->status == 'pagado')
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @elseif($payment->status == 'vencido')
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ ucfirst($payment->concept) }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $payment->enrollment->program->name }}
                                @if($payment->installment_number)
                                    - Cuota {{ $payment->installment_number }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                @if($payment->paid_at)
                                    Pagado el {{ $payment->paid_at->format('d/m/Y') }}
                                @else
                                    Vence el {{ $payment->due_date->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">S/ {{ number_format($payment->amount, 2) }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($payment->status == 'pagado') bg-emerald-100 text-emerald-800
                            @elseif($payment->status == 'pendiente') bg-amber-100 text-amber-800
                            @elseif($payment->status == 'vencido') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                        @if($payment->status == 'pagado')
                        <a href="{{ route('student.payment.receipt', $payment) }}" class="block mt-2 text-sm text-indigo-600 hover:text-indigo-800">
                            Ver recibo
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-4 text-gray-500">No tienes pagos registrados</p>
            </div>
            @endforelse
        </div>

        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

    <!-- Payment Methods Info -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Metodos de Pago Disponibles</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Efectivo</p>
                    <p class="text-xs text-gray-500">En secretaria</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Transferencia</p>
                    <p class="text-xs text-gray-500">BCP / Interbank</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-purple-600 font-bold text-sm">Y</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Yape</p>
                    <p class="text-xs text-gray-500">Al numero oficial</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                    <span class="text-teal-600 font-bold text-sm">P</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Plin</p>
                    <p class="text-xs text-gray-500">Al numero oficial</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
