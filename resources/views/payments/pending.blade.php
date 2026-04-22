@extends('layouts.app')

@section('title', 'Pagos Pendientes')
@section('page-title', 'Pagos Pendientes')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Pagos Pendientes</h2>
            <p class="text-sm text-gray-600">Lista de todos los pagos pendientes de cobro</p>
        </div>
        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Pagos
        </a>
    </div>

    {{-- Tabla de Pagos Pendientes --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estudiante</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Programa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Concepto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimiento</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payments as $payment)
                            @php
                                $isOverdue = $payment->due_date && $payment->due_date < now();
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isOverdue ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <span class="text-emerald-700 font-semibold text-sm">
                                                {{ strtoupper(substr($payment->student->name ?? 'N', 0, 1)) }}{{ strtoupper(substr($payment->student->last_name ?? 'A', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $payment->student->name ?? 'N/A' }} {{ $payment->student->last_name ?? '' }}</p>
                                            <p class="text-sm text-gray-500">{{ $payment->student->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-700">{{ $payment->enrollment->program->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-700">{{ $payment->concept ?? 'Cuota mensual' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900">S/ {{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                        {{ $payment->due_date ? $payment->due_date->format('d/m/Y') : 'Sin fecha' }}
                                    </span>
                                    @if($isOverdue)
                                        <p class="text-xs text-red-500">Vencido hace {{ $payment->due_date->diffForHumans() }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($isOverdue)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Vencido
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('payments.process', $payment) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Cobrar
                                        </a>
                                        <a href="{{ route('payments.show', $payment) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginacion --}}
            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No hay pagos pendientes</h3>
                <p class="text-gray-500">Todos los pagos estan al dia.</p>
            </div>
        @endif
    </div>
</div>
@endsection
