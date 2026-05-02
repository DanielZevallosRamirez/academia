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
                    <select name="concept" id="concept" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" onchange="handleConceptChange()">
                        <option value="">Seleccionar concepto...</option>
                        <option value="matricula" {{ old('concept') == 'matricula' ? 'selected' : '' }}>Matricula</option>
                        <option value="mensualidad" {{ old('concept') == 'mensualidad' ? 'selected' : '' }}>Mensualidad</option>
                        <option value="mensualidad_cuotas" {{ old('concept') == 'mensualidad_cuotas' ? 'selected' : '' }}>Mensualidad (Cuotas)</option>
                        <option value="pension" {{ old('concept') == 'pension' ? 'selected' : '' }}>Pension</option>
                        <option value="pension_cuotas" {{ old('concept') == 'pension_cuotas' ? 'selected' : '' }}>Pension (Cuotas)</option>
                        <option value="material" {{ old('concept') == 'material' ? 'selected' : '' }}>Material de estudio</option>
                        <option value="certificado" {{ old('concept') == 'certificado' ? 'selected' : '' }}>Certificado</option>
                        <option value="examen" {{ old('concept') == 'examen' ? 'selected' : '' }}>Examen</option>
                        <option value="otro" {{ old('concept') == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('concept')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="installment_number_container">
                    <label for="installment_number" class="block text-sm font-medium text-gray-700 mb-1">Numero de cuota</label>
                    <input type="number" name="installment_number" id="installment_number" value="{{ old('installment_number') }}" min="1" max="24" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Auto">
                    <p class="mt-1 text-xs text-gray-500" id="installment_hint">Se calcula automaticamente</p>
                    @error('installment_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total installments (only visible for concepts with _cuotas) -->
                <div id="total_installments_container" class="hidden">
                    <label for="total_installments" class="block text-sm font-medium text-gray-700 mb-1">Total de cuotas</label>
                    <input type="number" name="total_installments" id="total_installments" value="{{ old('total_installments', 1) }}" min="1" max="36" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: 6, 12...">
                    <p class="mt-1 text-xs text-gray-500">Numero total de cuotas para este concepto</p>
                    @error('total_installments')
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
                    
                    <!-- Upload Area -->
                    <div id="upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors cursor-pointer">
                        <input type="file" name="receipt_file" id="receipt_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="handleFileSelect(this)">
                        <label for="receipt_file" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Haz clic para subir o arrastra el archivo</p>
                            <p class="mt-1 text-xs text-gray-500">PDF, JPG o PNG hasta 5MB</p>
                        </label>
                    </div>
                    
                    <!-- Preview Area (hidden by default) -->
                    <div id="preview-area" class="hidden mt-3">
                        <div class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <!-- Image Preview (clickable) -->
                            <div id="image-preview-container" class="hidden flex-shrink-0 cursor-pointer group" onclick="openImageModal()">
                                <div class="relative">
                                    <img id="image-preview" src="" alt="Preview" class="w-20 h-20 object-cover rounded-lg border border-emerald-300 group-hover:opacity-75 transition-opacity">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-6 h-6 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-emerald-600 text-center mt-1">Click para ver</p>
                            </div>
                            <!-- PDF Icon -->
                            <div id="pdf-preview-container" class="hidden flex-shrink-0">
                                <div class="w-20 h-20 bg-red-100 rounded-lg border border-red-300 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/>
                                        <path d="M14 2v6h6M9 15h6M9 11h6"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- File Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm font-medium text-emerald-700">Archivo cargado correctamente</span>
                                </div>
                                <p id="file-name" class="mt-1 text-sm text-gray-600 truncate"></p>
                                <p id="file-size" class="text-xs text-gray-500"></p>
                            </div>
                            <!-- Remove Button -->
                            <button type="button" onclick="removeFile()" class="flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
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
<!-- Image Preview Modal -->
<div id="image-modal" class="fixed inset-0 z-50 hidden overflow-auto bg-black/80 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Close button -->
        <button onclick="closeImageModal()" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <!-- Image container -->
        <div class="relative max-w-4xl w-full">
            <img id="modal-image" src="" alt="Vista previa del comprobante" class="max-h-[85vh] w-auto mx-auto rounded-lg shadow-2xl">
            <p id="modal-filename" class="text-white/80 text-center mt-4 text-sm"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openImageModal() {
    const preview = document.getElementById('image-preview');
    const modal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');
    const modalFilename = document.getElementById('modal-filename');
    const fileName = document.getElementById('file-name');
    
    modalImage.src = preview.src;
    modalFilename.textContent = fileName.textContent;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('image-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Close modal when clicking outside the image
document.getElementById('image-modal')?.addEventListener('click', function(e) {
    if (e.target === this || e.target.classList.contains('min-h-screen')) {
        closeImageModal();
    }
});

function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    
    const uploadArea = document.getElementById('upload-area');
    const previewArea = document.getElementById('preview-area');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const imagePreview = document.getElementById('image-preview');
    const imageContainer = document.getElementById('image-preview-container');
    const pdfContainer = document.getElementById('pdf-preview-container');
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        alert('El archivo es muy grande. Maximo 5MB permitido.');
        input.value = '';
        return;
    }
    
    // Show preview area, hide upload area
    uploadArea.classList.add('hidden');
    previewArea.classList.remove('hidden');
    
    // Set file info
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    
    // Show appropriate preview based on file type
    if (file.type.startsWith('image/')) {
        imageContainer.classList.remove('hidden');
        pdfContainer.classList.add('hidden');
        
        // Create image preview
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        // PDF file
        imageContainer.classList.add('hidden');
        pdfContainer.classList.remove('hidden');
    }
}

function removeFile() {
    const input = document.getElementById('receipt_file');
    const uploadArea = document.getElementById('upload-area');
    const previewArea = document.getElementById('preview-area');
    const imagePreview = document.getElementById('image-preview');
    
    // Clear input
    input.value = '';
    
    // Reset preview
    imagePreview.src = '';
    
    // Show upload area, hide preview
    uploadArea.classList.remove('hidden');
    previewArea.classList.add('hidden');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Drag and drop support
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('upload-area');
    const input = document.getElementById('receipt_file');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.add('border-indigo-500', 'bg-indigo-50');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.classList.remove('border-indigo-500', 'bg-indigo-50');
        }, false);
    });
    
    uploadArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            input.files = files;
            handleFileSelect(input);
        }
    }, false);

    // Initialize concept change handler
    handleConceptChange();
    
    // Watch for enrollment change
    const enrollmentSelect = document.getElementById('enrollment_id');
    if (enrollmentSelect) {
        enrollmentSelect.addEventListener('change', updateInstallmentNumber);
    }
});

// Handle concept selection change
function handleConceptChange() {
    const concept = document.getElementById('concept').value;
    const totalInstallmentsContainer = document.getElementById('total_installments_container');
    const installmentHint = document.getElementById('installment_hint');
    
    // Check if concept requires installments (ends with _cuotas)
    const requiresInstallments = concept.endsWith('_cuotas');
    
    if (requiresInstallments) {
        totalInstallmentsContainer.classList.remove('hidden');
        installmentHint.textContent = 'Se calcula segun el historial';
    } else {
        totalInstallmentsContainer.classList.add('hidden');
        installmentHint.textContent = 'Se calcula automaticamente';
    }
    
    // Update installment number when concept changes
    updateInstallmentNumber();
}

// Fetch and update the next installment number
async function updateInstallmentNumber() {
    const enrollmentId = document.getElementById('enrollment_id').value;
    const concept = document.getElementById('concept').value;
    const installmentInput = document.getElementById('installment_number');
    const totalInstallmentsInput = document.getElementById('total_installments');
    const installmentHint = document.getElementById('installment_hint');
    const amountInput = document.getElementById('amount');
    const dueDateInput = document.getElementById('due_date');
    const paymentMethodInput = document.getElementById('payment_method');
    
    if (!enrollmentId || !concept) {
        installmentInput.value = '';
        installmentHint.textContent = 'Selecciona inscripcion y concepto';
        resetFieldsToEditable();
        return;
    }
    
    try {
        const response = await fetch(`{{ route('payments.next-installment') }}?enrollment_id=${enrollmentId}&concept=${concept}`);
        const data = await response.json();
        
        installmentInput.value = data.next_installment;
        
        // Check if this is a cuota-based concept
        const isCuotasConcept = concept.endsWith('_cuotas');
        
        if (data.is_first || data.is_plan_complete) {
            installmentHint.textContent = data.is_plan_complete ? 'Plan anterior completado - Nueva cuota 1' : 'Primera cuota para este concepto';
            // For new installment plans, allow editing all fields
            resetFieldsToEditable();
            
            if (isCuotasConcept) {
                // Use enrollment's num_installments for cuotas
                totalInstallmentsInput.value = data.enrollment_installments || 1;
                totalInstallmentsInput.readOnly = true;
                totalInstallmentsInput.classList.add('bg-gray-100');
            }
            
            // Set suggested amount based on concept type
            if (data.suggested_amount) {
                amountInput.value = data.suggested_amount;
                if (isCuotasConcept && !data.is_plan_complete) {
                    // Lock amount for cuotas to maintain consistency
                    amountInput.readOnly = true;
                    amountInput.classList.add('bg-gray-100');
                }
            } else if (concept === 'mensualidad' && data.program_price) {
                // Full mensualidad - use program price
                amountInput.value = data.program_price;
            }
        } else {
            installmentHint.textContent = `Cuota ${data.next_installment} de ${data.total_installments}`;
            
            // Pre-fill and disable fields from previous payment
            if (data.total_installments > 1) {
                totalInstallmentsInput.value = data.total_installments;
                totalInstallmentsInput.readOnly = true;
                totalInstallmentsInput.classList.add('bg-gray-100');
            }
            
            if (data.suggested_amount) {
                amountInput.value = data.suggested_amount;
                amountInput.readOnly = true;
                amountInput.classList.add('bg-gray-100');
            } else if (data.previous_amount) {
                amountInput.value = data.previous_amount;
                amountInput.readOnly = true;
                amountInput.classList.add('bg-gray-100');
            }
            
            if (data.previous_due_date) {
                dueDateInput.value = data.previous_due_date;
            }
            
            if (data.previous_payment_method) {
                paymentMethodInput.value = data.previous_payment_method;
            }
        }
    } catch (error) {
        console.error('Error fetching installment:', error);
        installmentInput.value = 1;
        installmentHint.textContent = 'Cuota 1 (nueva)';
        resetFieldsToEditable();
    }
}

// Reset fields to editable state
function resetFieldsToEditable() {
    const totalInstallmentsInput = document.getElementById('total_installments');
    const amountInput = document.getElementById('amount');
    const dueDateInput = document.getElementById('due_date');
    const paymentMethodInput = document.getElementById('payment_method');
    
    totalInstallmentsInput.readOnly = false;
    totalInstallmentsInput.classList.remove('bg-gray-100');
    
    amountInput.readOnly = false;
    amountInput.classList.remove('bg-gray-100');
    
    dueDateInput.readOnly = false;
    dueDateInput.classList.remove('bg-gray-100');
    
    paymentMethodInput.disabled = false;
    paymentMethodInput.classList.remove('bg-gray-100');
}
</script>
@endpush
@endsection
