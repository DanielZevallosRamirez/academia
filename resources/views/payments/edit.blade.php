@extends('layouts.app')

@section('title', 'Editar Pago')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('payments.show', $payment) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al detalle
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Editar Pago</h1>
        <p class="text-gray-600 mt-1">{{ $payment->invoice_number }}</p>
    </div>

    <form action="{{ route('payments.update', $payment) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Student Info (Read Only) -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Estudiante</h2>
            
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-700 font-semibold">
                    {{ strtoupper(substr($payment->user->name ?? 'NA', 0, 2)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $payment->user->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ $payment->enrollment->program->name ?? 'Sin programa' }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalles del Pago</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="concept" class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                    <select name="concept" id="concept" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar concepto...</option>
                        <option value="matricula" {{ old('concept', $payment->concept) == 'matricula' ? 'selected' : '' }}>Matricula</option>
                        <option value="mensualidad_cuotas" {{ old('concept', $payment->concept) == 'matricula_cuotas' ? 'selected' : '' }}>Matricula (Cuotas)</option>
                        <option value="mensualidad" {{ old('concept', $payment->concept) == 'mensualidad' ? 'selected' : '' }}>Mensualidad</option>
                        <option value="mensualidad_cuotas" {{ old('concept', $payment->concept) == 'mensualidad_cuotas' ? 'selected' : '' }}>Mensualidad (Cuotas)</option>
                        <option value="material" {{ old('concept', $payment->concept) == 'material' ? 'selected' : '' }}>Material de estudio</option>
                        <option value="certificado" {{ old('concept', $payment->concept) == 'certificado' ? 'selected' : '' }}>Certificado</option>
                        <option value="examen" {{ old('concept', $payment->concept) == 'examen' ? 'selected' : '' }}>Examen</option>
                        <option value="otro" {{ old('concept', $payment->concept) == 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('concept')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="installment_number" class="block text-sm font-medium text-gray-700 mb-1">Numero de cuota</label>
                    <input type="number" name="installment_number" id="installment_number" value="{{ old('installment_number', $payment->installment_number) }}" min="1" max="36" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: 1, 2, 3...">
                    @error('installment_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_installments" class="block text-sm font-medium text-gray-700 mb-1">Total de cuotas</label>
                    <input type="number" name="total_installments" id="total_installments" value="{{ old('total_installments', $payment->total_installments ?? 1) }}" min="1" max="36" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: 6, 12...">
                    <p class="mt-1 text-xs text-gray-500">Numero total de cuotas para este concepto</p>
                    @error('total_installments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Monto total</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">S/</span>
                        <input type="number" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" step="0.01" min="0" required class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                    </div>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Monto pagado</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">S/</span>
                        <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $payment->amount_paid) }}" step="0.01" min="0" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                    </div>
                    @error('amount_paid')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $payment->due_date?->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metodo de pago</label>
                    <select name="payment_method" id="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccionar metodo...</option>
                        <option value="efectivo" {{ old('payment_method', $payment->payment_method) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                        <option value="transferencia" {{ old('payment_method', $payment->payment_method) == 'transferencia' ? 'selected' : '' }}>Transferencia bancaria</option>
                        <option value="tarjeta" {{ old('payment_method', $payment->payment_method) == 'tarjeta' ? 'selected' : '' }}>Tarjeta de credito/debito</option>
                        <option value="yape" {{ old('payment_method', $payment->payment_method) == 'yape' ? 'selected' : '' }}>Yape</option>
                        <option value="plin" {{ old('payment_method', $payment->payment_method) == 'plin' ? 'selected' : '' }}>Plin</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="status" id="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pendiente" {{ old('status', $payment->status) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="parcial" {{ old('status', $payment->status) == 'parcial' ? 'selected' : '' }}>Parcial</option>
                        <option value="pagado" {{ old('status', $payment->status) == 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="vencido" {{ old('status', $payment->status) == 'vencido' ? 'selected' : '' }}>Vencido</option>
                        <option value="cancelado" {{ old('status', $payment->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="paid_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha de pago</label>
                    <input type="date" name="paid_date" id="paid_date" 
                           value="{{ old('paid_date', $payment->paid_date?->format('Y-m-d') ?? ($payment->amount_paid > 0 ? $payment->created_at->format('Y-m-d') : '')) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @if(!$payment->paid_date && $payment->amount_paid > 0)
                    <p class="mt-1 text-xs text-gray-500">Fecha de registro: {{ $payment->created_at->format('d/m/Y H:i') }}</p>
                    @endif
                    @error('paid_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas adicionales</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Observaciones o notas sobre el pago...">{{ old('notes', $payment->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Payment Proof -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Comprobante de Pago</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-1">Numero de transaccion/recibo</label>
                    <input type="text" name="transaction_id" id="transaction_id" value="{{ old('transaction_id', $payment->transaction_id) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: 001-000123">
                    @error('transaction_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if($payment->payment_proof)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comprobante actual</label>
                    <div class="flex items-center gap-4">
                        <img src="{{ $payment->payment_proof_url }}" alt="Comprobante" class="w-24 h-24 object-cover rounded-lg border cursor-pointer" onclick="openImageModal()">
                        <div class="text-sm text-gray-500">
                            <p>Archivo actual cargado</p>
                            <p class="text-xs">Sube otro archivo para reemplazarlo</p>
                        </div>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $payment->payment_proof ? 'Reemplazar comprobante' : 'Adjuntar comprobante' }}</label>
                    
                    <!-- Upload Area -->
                    <div id="upload-area" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors cursor-pointer">
                        <input type="file" name="payment_proof" id="payment_proof" accept=".jpg,.jpeg,.png" class="hidden" onchange="handleFileSelect(this)">
                        <label for="payment_proof" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Haz clic para subir o arrastra el archivo</p>
                            <p class="mt-1 text-xs text-gray-500">JPG o PNG hasta 5MB</p>
                        </label>
                    </div>
                    
                    <!-- Preview Area -->
                    <div id="preview-area" class="hidden mt-3">
                        <div class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <div id="image-preview-container" class="flex-shrink-0 cursor-pointer" onclick="openNewImageModal()">
                                <img id="image-preview" src="" alt="Preview" class="w-20 h-20 object-cover rounded-lg border border-emerald-300">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm font-medium text-emerald-700">Nuevo archivo seleccionado</span>
                                </div>
                                <p id="file-name" class="mt-1 text-sm text-gray-600 truncate"></p>
                                <p id="file-size" class="text-xs text-gray-500"></p>
                            </div>
                            <button type="button" onclick="removeFile()" class="flex-shrink-0 p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    @error('payment_proof')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('payments.show', $payment) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<!-- Image Modal for existing proof -->
@if($payment->payment_proof)
<div id="image-modal" class="fixed inset-0 z-50 hidden overflow-auto bg-black/80 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="relative max-w-4xl w-full">
            <img src="{{ $payment->payment_proof_url }}" alt="Comprobante actual" class="max-h-[85vh] w-auto mx-auto rounded-lg shadow-2xl">
        </div>
    </div>
</div>
@endif

<!-- Image Modal for new file -->
<div id="new-image-modal" class="fixed inset-0 z-50 hidden overflow-auto bg-black/80 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <button onclick="closeNewImageModal()" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white bg-black/20 hover:bg-black/40 rounded-full transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="relative max-w-4xl w-full">
            <img id="modal-new-image" src="" alt="Vista previa" class="max-h-[85vh] w-auto mx-auto rounded-lg shadow-2xl">
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    
    const uploadArea = document.getElementById('upload-area');
    const previewArea = document.getElementById('preview-area');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const imagePreview = document.getElementById('image-preview');
    
    if (file.size > 5 * 1024 * 1024) {
        alert('El archivo es muy grande. Maximo 5MB permitido.');
        input.value = '';
        return;
    }
    
    uploadArea.classList.add('hidden');
    previewArea.classList.remove('hidden');
    
    fileName.textContent = file.name;
    fileSize.textContent = formatFileSize(file.size);
    
    const reader = new FileReader();
    reader.onload = function(e) {
        imagePreview.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function removeFile() {
    const input = document.getElementById('payment_proof');
    const uploadArea = document.getElementById('upload-area');
    const previewArea = document.getElementById('preview-area');
    
    input.value = '';
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

function openImageModal() {
    document.getElementById('image-modal')?.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('image-modal')?.classList.add('hidden');
    document.body.style.overflow = '';
}

function openNewImageModal() {
    const preview = document.getElementById('image-preview');
    const modalImage = document.getElementById('modal-new-image');
    modalImage.src = preview.src;
    document.getElementById('new-image-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeNewImageModal() {
    document.getElementById('new-image-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
        closeNewImageModal();
    }
});
</script>
@endpush
@endsection
