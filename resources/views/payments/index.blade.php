@extends('layouts.app')

@section('title', 'Gestión de Pagos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Pagos</h1>
            <p class="text-gray-600 mt-1">Administra los pagos y cuotas de los estudiantes</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('payments.pending') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pendientes
            </a>
            <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Registrar Pago
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Recaudado este mes</p>
                    <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['total_pagado_mes'] ?? 0, 2) }}</p>
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
                    <p class="text-sm text-gray-600">Pagos pendientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['cantidad_pendientes'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Pagos vencidos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['cantidad_vencidos'] ?? 0 }}</p>
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
                    <p class="text-sm text-gray-600">Total pendiente</p>
                    <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($stats['total_pendiente'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form action="{{ route('payments.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por estudiante o concepto..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex gap-3">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los estados</option>
                    <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="pagado" {{ request('status') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                    <option value="vencido" {{ request('status') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                    <option value="parcial" {{ request('status') == 'parcial' ? 'selected' : '' }}>Parcial</option>
                </select>
                <select name="method" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Todos los metodos</option>
                    <option value="efectivo" {{ request('method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                    <option value="transferencia" {{ request('method') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                    <option value="tarjeta" {{ request('method') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                    <option value="yape" {{ request('method') == 'yape' ? 'selected' : '' }}>Yape</option>
                    <option value="plin" {{ request('method') == 'plin' ? 'selected' : '' }}>Plin</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-3 items-center">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Desde:</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Hasta:</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <select name="period" onchange="applyPeriodFilter(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Periodo</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hoy</option>
                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Esta semana</option>
                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Este mes</option>
                    <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Este ano</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800">
                    Filtrar
                </button>
                @if(request()->hasAny(['search', 'status', 'method', 'date_from', 'date_to', 'period']))
                <a href="{{ route('payments.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    Limpiar
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metodo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    @if($payment->enrollment->student->photo)
                                        <img src="{{ Storage::url($payment->enrollment->student->photo) }}" class="w-10 h-10 rounded-full object-cover" alt="">
                                    @else
                                        <span class="text-sm font-medium text-gray-600">{{ substr($payment->enrollment->student->name, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $payment->enrollment->student->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->enrollment->program->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $payment->concept }}</p>
                            @if($payment->installment_number)
                                <p class="text-xs text-gray-500">Cuota {{ $payment->installment_number }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-semibold text-gray-900">S/ {{ number_format($payment->amount, 2) }}</p>
                            @if($payment->amount_paid && $payment->amount_paid < $payment->amount)
                                <p class="text-xs text-amber-600">Pagado: S/ {{ number_format($payment->amount_paid, 2) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($payment->payment_method == 'efectivo') bg-green-100 text-green-800
                                @elseif($payment->payment_method == 'transferencia') bg-blue-100 text-blue-800
                                @elseif($payment->payment_method == 'tarjeta') bg-purple-100 text-purple-800
                                @else bg-orange-100 text-orange-800 @endif">
                                {{ ucfirst($payment->payment_method ?? 'Sin definir') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($payment->status == 'pagado') bg-emerald-100 text-emerald-800
                                @elseif($payment->status == 'pendiente') bg-amber-100 text-amber-800
                                @elseif($payment->status == 'vencido') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($payment->paid_at)
                                {{ $payment->paid_at->format('d/m/Y H:i') }}
                            @else
                                <span class="text-amber-600">Vence: {{ $payment->due_date->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @if($payment->status != 'pagado')
                                <a href="{{ route('payments.process', $payment) }}" class="text-emerald-600 hover:text-emerald-900" title="Procesar pago">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </a>
                                @endif
                                @if($payment->payment_proof)
                                <a href="{{ $payment->payment_proof_url }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="Ver comprobante">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                                @else
                                <button type="button" onclick="openUploadModal({{ $payment->id }})" class="text-amber-600 hover:text-amber-900" title="Subir comprobante">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </button>
                                @endif
                                <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-900" title="Ver detalles">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('payments.receipt', $payment) }}" class="text-gray-600 hover:text-gray-900" title="Imprimir recibo">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-4 text-gray-500">No se encontraron pagos</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeUploadModal()"></div>
        
        <div class="relative bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
            <div class="text-left">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Comprobante de Pago</h3>
                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto del comprobante</label>
                        <input type="file" name="payment_proof" accept="image/*" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="mt-1 text-xs text-gray-500">Formatos: JPG, PNG. Maximo 5MB</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            Subir Comprobante
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openUploadModal(paymentId) {
    const modal = document.getElementById('uploadModal');
    const form = document.getElementById('uploadForm');
    form.action = `/payments/${paymentId}/upload-proof`;
    modal.classList.remove('hidden');
}

function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    modal.classList.add('hidden');
}

function applyPeriodFilter(period) {
    const dateFrom = document.querySelector('input[name="date_from"]');
    const dateTo = document.querySelector('input[name="date_to"]');
    const today = new Date();
    
    let fromDate, toDate;
    
    switch(period) {
        case 'today':
            fromDate = toDate = formatDate(today);
            break;
        case 'week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            fromDate = formatDate(startOfWeek);
            toDate = formatDate(today);
            break;
        case 'month':
            fromDate = formatDate(new Date(today.getFullYear(), today.getMonth(), 1));
            toDate = formatDate(today);
            break;
        case 'year':
            fromDate = formatDate(new Date(today.getFullYear(), 0, 1));
            toDate = formatDate(today);
            break;
        default:
            return;
    }
    
    dateFrom.value = fromDate;
    dateTo.value = toDate;
}

function formatDate(date) {
    return date.toISOString().split('T')[0];
}
</script>
@endpush
@endsection
