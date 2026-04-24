@extends('layouts.app')

@section('title', 'Detalle de Pago')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('payments.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detalle de Pago</h1>
                <p class="text-gray-600 mt-1">{{ $payment->invoice_number }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            @if($payment->status !== 'pagado')
            <a href="{{ route('payments.process', $payment) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Procesar Pago
            </a>
            @endif
            <a href="{{ route('payments.receipt', $payment) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Recibo
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Informacion del Pago</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Numero de Factura</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $payment->invoice_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $payment->status === 'pagado' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $payment->status === 'pendiente' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $payment->status === 'parcial' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $payment->status === 'vencido' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $payment->status === 'cancelado' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Monto Total</dt>
                            <dd class="mt-1 text-2xl font-bold text-gray-900">S/ {{ number_format($payment->amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Monto Pagado</dt>
                            <dd class="mt-1 text-2xl font-bold {{ $payment->amount_paid > 0 ? 'text-emerald-600' : 'text-gray-400' }}">
                                S/ {{ number_format($payment->amount_paid ?? 0, 2) }}
                                @if($payment->amount_paid > 0 && $payment->amount_paid < $payment->amount)
                                <span class="text-sm font-normal text-gray-500">(Restante: S/ {{ number_format($payment->amount - $payment->amount_paid, 2) }})</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Metodo de Pago</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($payment->payment_method ?? 'No especificado') }}</dd>
                        </div>
                        @if($payment->concept)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Concepto</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ ucfirst($payment->concept) }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Vencimiento</dt>
                            <dd class="mt-1 text-lg text-gray-900">{{ $payment->due_date ? $payment->due_date->format('d/m/Y') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Pago</dt>
                            <dd class="mt-1 text-lg text-gray-900">
                                @if($payment->paid_date)
                                    {{ $payment->paid_date->format('d/m/Y') }}
                                @elseif($payment->amount_paid > 0)
                                    {{ $payment->created_at->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        @if($payment->installment_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Numero de Cuota</dt>
                            <dd class="mt-1 text-lg text-gray-900">Cuota {{ $payment->installment_number }}</dd>
                        </div>
                        @endif
                        @if($payment->transaction_id)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID de Transaccion</dt>
                            <dd class="mt-1 text-lg text-gray-900 font-mono">{{ $payment->transaction_id }}</dd>
                        </div>
                        @endif
                    </dl>
                    
                    @if($payment->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-sm font-medium text-gray-500">Notas</dt>
                        <dd class="mt-2 text-gray-700">{{ $payment->notes }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Proof -->
            @if($payment->payment_proof)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Comprobante de Pago</h2>
                </div>
                <div class="p-6">
                    <div class="relative group cursor-pointer" onclick="openProofModal()">
                        <img src="{{ asset('storage/' . $payment->payment_proof) }}" 
                             alt="Comprobante de pago" 
                             class="w-full max-w-md mx-auto rounded-lg border border-gray-200 shadow-sm group-hover:opacity-90 transition-opacity">
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="bg-black/50 text-white px-4 py-2 rounded-lg text-sm">Click para ampliar</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Student Info -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Estudiante</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            {{ strtoupper(substr($payment->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $payment->user->name }} {{ $payment->user->last_name }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->user->email }}</p>
                            @if($payment->user->dni)
                            <p class="text-sm text-gray-500">DNI: {{ $payment->user->dni }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('students.show', $payment->user) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                            Ver perfil del estudiante
                        </a>
                    </div>
                </div>
            </div>

            <!-- Program Info -->
            @if($payment->enrollment && $payment->enrollment->program)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Programa</h2>
                </div>
                <div class="p-6">
                    <p class="font-semibold text-gray-900">{{ $payment->enrollment->program->name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $payment->enrollment->program->modality ?? 'Presencial' }}</p>
                    @if($payment->enrollment->program->price)
                    <p class="text-lg font-bold text-emerald-600 mt-2">S/ {{ number_format($payment->enrollment->program->price, 2) }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Acciones</h2>
                </div>
                <div class="p-4 space-y-2">
                    @if($payment->status !== 'pagado')
                    <a href="{{ route('payments.process', $payment) }}" class="flex items-center gap-3 w-full px-4 py-3 text-left text-gray-700 hover:bg-emerald-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Procesar Pago</span>
                    </a>
                    @endif
                    <a href="{{ route('payments.edit', $payment) }}" class="flex items-center gap-3 w-full px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Editar Pago</span>
                    </a>
                    <a href="{{ route('payments.receipt', $payment) }}" class="flex items-center gap-3 w-full px-4 py-3 text-left text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Imprimir Recibo</span>
                    </a>
                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" 
                          onsubmit="return confirm('¿Estas seguro de eliminar este pago?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-left text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Eliminar Pago</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Proof Modal -->
@if($payment->payment_proof)
<div id="proof-modal" class="fixed inset-0 z-50 hidden overflow-auto bg-black/80 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <button onclick="closeProofModal()" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="relative max-w-4xl w-full">
            <img src="{{ asset('storage/' . $payment->payment_proof) }}" alt="Comprobante de pago" class="max-h-[85vh] w-auto mx-auto rounded-lg shadow-2xl">
        </div>
    </div>
</div>

@push('scripts')
<script>
function openProofModal() {
    document.getElementById('proof-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProofModal() {
    document.getElementById('proof-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeProofModal();
});

document.getElementById('proof-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeProofModal();
});
</script>
@endpush
@endif
@endsection
