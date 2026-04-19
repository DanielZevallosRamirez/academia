@extends('layouts.app')

@section('title', 'Registrar Pago')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('payments.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a pagos
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Registrar Nuevo Pago</h1>
        <p class="text-gray-600 mt-1">Registra un pago manual para un estudiante</p>
    </div>

    <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Student Selection -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Estudiante</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="enrollment_id" class="block text-sm font-medium text-gray-700 mb-1">Seleccionar inscripcion</label>
                    <select name="enrollment_id" id="enrollment_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar estudiante e inscripcion...</option>
                        @foreach($enrollments as $enrollment)
                            <option value="{{ $enrollment->id }}" {{ old('enrollment_id') == $enrollment->id ? 'selected' : '' }}>
                                {{ $enrollment->student->name }} - {{ $enrollment->program->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('enrollment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Pago</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="concept" class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                    <select name="concept" id="concept" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar concepto...</option>
                        <option value="matricula" {{ old('concept') == 'matricula' ? 'selected' : '' }}>Matricula</option>
                        <option value="mensualidad" {{ old('concept') == 'mensualidad' ? 'selected' : '' }}>Mensualidad</option>
                        <option value="material" {{ old('concept') == 'material' ? 'selected' : '' }}>Material de estudio</option>
                        <option value="certificado" {{ old('concept') == 'certificado' ? 'selected' : '' }}>Certificado</option>
                        <option value="examen" {{ old('concept') == 'examen' ? 'selected' : '' }}>Examen</option>
                        <option value="otro" {{ old('concept') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('concept')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="installment_number" class="block text-sm font-medium text-gray-700 mb-1">Numero de cuota (opcional)</label>
                    <input type="number" name="installment_number" id="installment_number" value="{{ old('installment_number') }}" min="1" max="24" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: 1, 2, 3...">
                    @error('installment_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Monto total</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">S/</span>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Monto pagado</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">S/</span>
                        <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" step="0.01" min="0" required class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                    </div>
                    @error('amount_paid')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metodo de pago</label>
                    <select name="payment_method" id="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar metodo...</option>
                        <option value="efectivo" {{ old('payment_method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ old('payment_method') == 'transferencia' ? 'selected' : '' }}>Transferencia bancaria</option>
                        <option value="tarjeta" {{ old('payment_method') == 'tarjeta' ? 'selected' : '' }}>Tarjeta de credito/debito</option>
                        <option value="yape" {{ old('payment_method') == 'yape' ? 'selected' : '' }}>Yape</option>
                        <option value="plin" {{ old('payment_method') == 'plin' ? 'selected' : '' }}>Plin</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas adicionales (opcional)</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Observaciones o notas sobre el pago...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Comprobante -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Comprobante de Pago</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="receipt_number" class="block text-sm font-medium text-gray-700 mb-1">Numero de recibo/comprobante</label>
                    <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: 001-000123">
                    @error('receipt_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adjuntar comprobante (opcional)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors">
                        <input type="file" name="receipt_file" id="receipt_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                        <label for="receipt_file" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Haz clic para subir o arrastra el archivo</p>
                            <p class="mt-1 text-xs text-gray-500">PDF, JPG o PNG hasta 5MB</p>
                        </label>
                    </div>
                    @error('receipt_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Estado del Pago</h2>
            
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center">
                    <input type="radio" name="status" value="pagado" {{ old('status', 'pagado') == 'pagado' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                    <span class="ml-2 text-sm text-gray-700">Pagado completamente</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="parcial" {{ old('status') == 'parcial' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                    <span class="ml-2 text-sm text-gray-700">Pago parcial</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="status" value="pendiente" {{ old('status') == 'pendiente' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Pendiente (solo registrar)</span>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('payments.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
                Registrar Pago
            </button>
        </div>
    </form>
</div>
@endsection
