@extends('layouts.app')

@section('title', 'Mis Pagos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Mis Pagos</h1>
        <p class="text-gray-600 mt-1">Consulta el estado de tus pagos y cuotas pendientes</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Total Pendiente -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pendiente</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">S/ {{ number_format($stats['total_pendiente'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Pagado -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pagado</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">S/ {{ number_format($stats['total_pagado'] ?? 0, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Proximo Pago -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Proximo Pago</p>
                    @if($stats['proximo_pago'])
                        @php
                            $proximoPago = $stats['proximo_pago'];
                            $montoRestante = $proximoPago->amount - ($proximoPago->amount_paid ?? 0);
                        @endphp
                        <p class="text-2xl font-bold text-indigo-600 mt-1">S/ {{ number_format($montoRestante, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Vence: {{ $proximoPago->due_date->format('d/m/Y') }}</p>
                    @else
                        <p class="text-lg font-medium text-gray-400 mt-1">Sin pagos pendientes</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Historial de Pagos</h2>
        </div>

        @if($payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Vencimiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Pago</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900">{{ $payment->concept ?? 'Cuota mensual' }}</p>
                            @if($payment->installment_number)
                                <p class="text-xs text-gray-500">{{ $payment->installment_label }}</p>
                            @elseif($payment->description)
                                <p class="text-xs text-gray-500">{{ $payment->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-900">{{ $payment->enrollment->program->name ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900">S/ {{ number_format($payment->amount, 2) }}</p>
                            @if($payment->real_status == 'parcial' && $payment->amount_paid > 0)
                                <p class="text-xs text-green-600 font-medium">Pagado: S/ {{ number_format($payment->amount_paid, 2) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600">{{ $payment->due_date->format('d/m/Y') }}</p>
                            @if($payment->real_status == 'pendiente' && $payment->due_date->isPast())
                                <span class="text-xs text-red-600 font-medium">Vencido</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($payment->paid_date)
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($payment->paid_date)->format('d/m/Y') }}</p>
                            @elseif($payment->paid_at)
                                <p class="text-sm text-gray-600">{{ $payment->paid_at->format('d/m/Y') }}</p>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $payment->status_badge }}">
                                {{ $payment->status_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium">No hay pagos registrados</p>
            <p class="text-sm text-gray-500 mt-1">Los pagos apareceran aqui cuando se generen</p>
        </div>
        @endif
    </div>
</div>
@endsection
