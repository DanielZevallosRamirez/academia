@extends('layouts.app')

@section('title', 'Procesar Pago')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('payments.show', $payment) }}" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Procesar Pago</h1>
            <p class="text-gray-600 mt-1">{{ $payment->invoice_number }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Registrar Pago</h2>
                </div>
                <form action="{{ route('payments.process.store', $payment) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    
                    <!-- Amount to Pay - Compact Display -->
                    @php
                        $pendingAmount = $payment->amount - ($payment->amount_paid ?? 0);
                    @endphp
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-white/80">Monto a Cobrar</p>
                                <p class="text-2xl font-bold">S/ {{ number_format($pendingAmount, 2) }}</p>
                            </div>
                        </div>
                        @if($payment->installment_number && $payment->total_installments)
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 bg-white/20 rounded-full text-sm font-medium">
                                Cuota {{ $payment->installment_number }}/{{ $payment->total_installments }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Hidden input for amount -->
                    <input type="hidden" name="amount_paid" value="{{ $pendingAmount }}"

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Metodo de Pago <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @php
                                $methods = [
                                    'efectivo' => ['icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Efectivo'],
                                    'transferencia' => ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label' => 'Transferencia'],
                                    'tarjeta' => ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label' => 'Tarjeta'],
                                    'yape' => ['icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', 'label' => 'Yape/Plin'],
                                ];
                                $currentMethod = old('payment_method', $payment->payment_method ?? 'efectivo');
                            @endphp
                            @foreach($methods as $value => $method)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="payment_method" value="{{ $value }}" 
                                       class="peer sr-only" {{ $currentMethod === $value ? 'checked' : '' }} required>
                                <div class="flex flex-col items-center gap-2 p-4 border-2 border-gray-200 rounded-xl peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-gray-300 transition-colors">
                                    <svg class="w-6 h-6 text-gray-500 peer-checked:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $method['icon'] }}"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ $method['label'] }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction ID -->
                    <div>
                        <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Numero de Operacion / Referencia
                        </label>
                        <input type="text" 
                               name="transaction_id" 
                               id="transaction_id"
                               value="{{ old('transaction_id') }}"
                               placeholder="Ej: 00012345678"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('transaction_id') border-red-500 @enderror">
                        @error('transaction_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Proof Upload -->
                    <div>
                        <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-2">
                            Comprobante de Pago (Foto o PDF)
                        </label>
                        
                        <!-- Upload Zone (hidden when preview is shown) -->
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-emerald-400 transition-colors" id="dropzone">
                            <div class="space-y-2 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none">
                                        <span>Subir archivo</span>
                                        <input id="payment_proof" name="payment_proof" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    JPG, PNG o PDF hasta 5MB
                                </p>
                            </div>
                        </div>
                        
                        <!-- Image/PDF Preview Container -->
                        <div id="preview-container" class="hidden mt-2">
                            <div class="relative bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                <!-- Image Preview -->
                                <div id="image-preview-wrapper" class="hidden">
                                    <img id="image-preview" src="" alt="Vista previa" class="w-full max-h-64 object-contain cursor-pointer hover:opacity-90 transition-opacity" onclick="openLightbox()">
                                    <div class="absolute bottom-2 right-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-black/60 text-white text-xs rounded">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                            </svg>
                                            Click para ampliar
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- PDF Preview -->
                                <div id="pdf-preview-wrapper" class="hidden p-6 text-center">
                                    <div class="w-16 h-16 mx-auto bg-red-100 rounded-lg flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p id="pdf-name" class="text-sm font-medium text-gray-700 truncate"></p>
                                    <p class="text-xs text-gray-500 mt-1">Documento PDF</p>
                                </div>
                                
                                <!-- File info bar -->
                                <div class="flex items-center justify-between p-3 bg-white border-t border-gray-200">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span id="file-name" class="text-sm text-gray-700 truncate"></span>
                                    </div>
                                    <button type="button" id="remove-file" class="flex items-center gap-1 px-2 py-1 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @error('payment_proof')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($payment->payment_proof)
                        <div class="mt-2 flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span>Ya existe un comprobante:</span>
                            <a href="{{ Storage::url($payment->payment_proof) }}" target="_blank" class="text-emerald-600 hover:underline">
                                Ver comprobante actual
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notas Adicionales
                        </label>
                        <textarea name="notes" 
                                  id="notes"
                                  rows="3"
                                  placeholder="Observaciones del pago..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Confirmar Pago
                        </button>
                        <a href="{{ route('payments.show', $payment) }}" class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar - Payment Info -->
        <div class="space-y-6">
            <!-- Student Info -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Estudiante</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            {{ strtoupper(substr($payment->student->name ?? $payment->user->name ?? 'NA', 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $payment->student->name ?? $payment->user->name ?? 'N/A' }} 
                                {{ $payment->student->last_name ?? $payment->user->last_name ?? '' }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $payment->student->email ?? $payment->user->email ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Detalles del Pago</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Factura</p>
                        <p class="font-semibold text-gray-900">{{ $payment->invoice_number }}</p>
                    </div>
                    @if($payment->enrollment && $payment->enrollment->program)
                    <div>
                        <p class="text-sm text-gray-500">Programa</p>
                        <p class="font-semibold text-gray-900">{{ $payment->enrollment->program->name }}</p>
                    </div>
                    @endif
                    @if($payment->concept)
                    <div>
                        <p class="text-sm text-gray-500">Concepto</p>
                        <p class="font-semibold text-gray-900">
                            {{ str_replace('_', ' ', ucfirst($payment->concept)) }}
                            @if($payment->installment_number && $payment->total_installments)
                                <span class="text-gray-500">(Cuota {{ $payment->installment_number }}/{{ $payment->total_installments }})</span>
                            @endif
                        </p>
                    </div>
                    @endif
                    <div>
                        <p class="text-sm text-gray-500">Fecha de Vencimiento</p>
                        <p class="font-semibold {{ $payment->due_date && $payment->due_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $payment->due_date ? $payment->due_date->format('d/m/Y') : '-' }}
                            @if($payment->due_date && $payment->due_date->isPast())
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full ml-2">Vencido</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estado Actual</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $payment->status_badge }}">
                            {{ $payment->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-amber-800">Importante</p>
                        <p class="text-sm text-amber-700 mt-1">
                            Verifica el monto y metodo de pago antes de confirmar. Esta accion no se puede deshacer facilmente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 z-50 hidden" onclick="closeLightbox(event)">
    <div class="absolute inset-0 bg-black/90"></div>
    <div class="relative flex items-center justify-center w-full h-full p-4">
        <button type="button" onclick="closeLightbox()" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white transition-colors z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <img id="lightbox-image" src="" alt="Comprobante" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
    </div>
</div>
@endsection

@push('scripts')
<script>
// Lightbox functions
function openLightbox() {
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightbox-image');
    const previewImage = document.getElementById('image-preview');
    
    if (previewImage && previewImage.src) {
        lightboxImage.src = previewImage.src;
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeLightbox(event) {
    if (event && event.target !== event.currentTarget && !event.target.closest('button')) {
        return;
    }
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.add('hidden');
    document.body.style.overflow = '';
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('payment_proof');
    const previewContainer = document.getElementById('preview-container');
    const imagePreviewWrapper = document.getElementById('image-preview-wrapper');
    const pdfPreviewWrapper = document.getElementById('pdf-preview-wrapper');
    const imagePreview = document.getElementById('image-preview');
    const pdfName = document.getElementById('pdf-name');
    const fileName = document.getElementById('file-name');
    const removeBtn = document.getElementById('remove-file');

    // Handle file selection
    fileInput.addEventListener('change', function(e) {
        handleFile(e.target.files[0]);
    });

    // Drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropzone.classList.add('border-emerald-500', 'bg-emerald-50');
    }

    function unhighlight() {
        dropzone.classList.remove('border-emerald-500', 'bg-emerald-50');
    }

    dropzone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            fileInput.files = files;
            handleFile(files[0]);
        }
    });

    function handleFile(file) {
        if (!file) return;
        
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            alert('Por favor sube un archivo JPG, PNG o PDF.');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('El archivo no debe superar los 5MB.');
            return;
        }

        fileName.textContent = file.name;
        dropzone.classList.add('hidden');
        previewContainer.classList.remove('hidden');
        
        if (file.type === 'application/pdf') {
            // Show PDF preview
            imagePreviewWrapper.classList.add('hidden');
            pdfPreviewWrapper.classList.remove('hidden');
            pdfName.textContent = file.name;
        } else {
            // Show image preview
            pdfPreviewWrapper.classList.add('hidden');
            imagePreviewWrapper.classList.remove('hidden');
            
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function resetUpload() {
        fileInput.value = '';
        previewContainer.classList.add('hidden');
        dropzone.classList.remove('hidden');
        imagePreviewWrapper.classList.add('hidden');
        pdfPreviewWrapper.classList.add('hidden');
        imagePreview.src = '';
    }

    removeBtn.addEventListener('click', resetUpload);
});
</script>
@endpush
