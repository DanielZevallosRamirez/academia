@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('header', 'Editar Perfil')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<style>
    .cropper-modal { background-color: rgba(0,0,0,0.8); }
    .img-container { max-height: 400px; }
    .img-container img { max-width: 100%; display: block; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Mensajes de exito --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Errores de validacion --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario Principal Unificado --}}
    <form action="{{ route('profile.update') }}" method="POST" id="profileForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="cropped_photo" id="croppedPhotoInput">

        {{-- Seccion: Foto de Perfil --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Foto de Perfil
                </h3>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-6">
                    {{-- Preview de foto actual --}}
                    <div class="relative">
                        <div id="currentPhotoContainer" class="w-28 h-28 rounded-2xl overflow-hidden bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center flex-shrink-0 shadow-lg">
                            @if($user->photo)
                                <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" id="currentPhoto" class="w-full h-full object-cover">
                            @else
                                <span id="currentInitials" class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1) . substr($user->last_name ?? '', 0, 1)) }}</span>
                            @endif
                        </div>
                        @if($user->photo)
                        <button type="button" id="deletePhotoBtn" class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-colors" title="Eliminar foto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @endif
                    </div>
                    
                    <div class="flex-1">
                        <label class="block">
                            <span class="sr-only">Seleccionar foto</span>
                            <input type="file" id="photoInput" accept="image/*"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                        </label>
                        <p class="mt-2 text-xs text-slate-500">PNG, JPG o GIF. Maximo 2MB. La imagen se recortara en formato cuadrado.</p>
                    </div>
                </div>

                {{-- Preview de nueva foto seleccionada --}}
                <div id="newPhotoPreview" class="mt-4 hidden">
                    <div class="p-4 bg-slate-50 rounded-xl border-2 border-dashed border-slate-300">
                        <p class="text-sm font-medium text-slate-700 mb-3">Vista previa de nueva foto:</p>
                        <div class="flex items-center gap-4">
                            <div id="croppedPreview" class="w-20 h-20 rounded-xl overflow-hidden bg-slate-200"></div>
                            <div class="flex gap-2">
                                <button type="button" id="editCropBtn" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-medium transition-colors">
                                    Ajustar recorte
                                </button>
                                <button type="button" id="cancelPhotoBtn" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-colors">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seccion: Informacion Personal --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Informacion Personal
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nombre --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>

                    {{-- Apellido --}}
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-slate-700 mb-2">Apellido</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Correo Electronico *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>

                    {{-- Telefono --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">Telefono</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>

                    {{-- DNI --}}
                    <div>
                        <label for="dni" class="block text-sm font-medium text-slate-700 mb-2">DNI / Documento</label>
                        <input type="text" name="dni" id="dni" value="{{ old('dni', $user->dni) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>

                    {{-- Fecha de nacimiento --}}
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-slate-700 mb-2">Fecha de Nacimiento</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>

                    {{-- Direccion --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-slate-700 mb-2">Direccion</label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none">{{ old('address', $user->address) }}</textarea>
                    </div>

                    {{-- Contacto de emergencia --}}
                    <div>
                        <label for="emergency_contact" class="block text-sm font-medium text-slate-700 mb-2">Contacto de Emergencia</label>
                        <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact', $user->emergency_contact) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>

                    {{-- Telefono de emergencia --}}
                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-slate-700 mb-2">Telefono de Emergencia</label>
                        <input type="text" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone', $user->emergency_phone) }}"
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de accion al final --}}
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('profile.show') }}" class="px-6 py-3 text-slate-700 hover:bg-slate-100 rounded-xl font-medium transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/30 transition-all">
                Guardar Todos los Cambios
            </button>
        </div>
    </form>

    {{-- Formulario de Contrasena (separado) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Cambiar Contrasena
            </h3>
        </div>
        <form action="{{ route('profile.password') }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">Contrasena Actual</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Nueva Contrasena</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirmar Contrasena</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 pt-6 border-t border-slate-200">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl font-medium shadow-lg shadow-amber-500/30 transition-all">
                    Actualizar Contrasena
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal para recortar imagen --}}
<div id="cropModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/70" id="cropModalOverlay"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Ajustar Foto de Perfil</h3>
                <button type="button" id="closeCropModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <div class="img-container bg-slate-100 rounded-xl overflow-hidden">
                    <img id="cropImage" src="" alt="Imagen a recortar">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                <div class="flex gap-2">
                    <button type="button" id="rotateLeft" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors" title="Rotar izquierda">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </button>
                    <button type="button" id="rotateRight" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors" title="Rotar derecha">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6"/>
                        </svg>
                    </button>
                    <button type="button" id="zoomIn" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors" title="Acercar">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                        </svg>
                    </button>
                    <button type="button" id="zoomOut" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors" title="Alejar">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                        </svg>
                    </button>
                </div>
                <div class="flex gap-3">
                    <button type="button" id="cancelCrop" class="px-4 py-2 text-slate-700 hover:bg-slate-100 rounded-xl font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="button" id="applyCrop" class="px-6 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-medium shadow-lg shadow-emerald-500/30 transition-all">
                        Aplicar Recorte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let cropper = null;
    let croppedImageData = null;
    
    const photoInput = document.getElementById('photoInput');
    const cropModal = document.getElementById('cropModal');
    const cropImage = document.getElementById('cropImage');
    const croppedPhotoInput = document.getElementById('croppedPhotoInput');
    const newPhotoPreview = document.getElementById('newPhotoPreview');
    const croppedPreview = document.getElementById('croppedPreview');
    const currentPhotoContainer = document.getElementById('currentPhotoContainer');
    const deletePhotoBtn = document.getElementById('deletePhotoBtn');

    // Abrir modal cuando se selecciona una imagen
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('La imagen no debe superar los 2MB');
                photoInput.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                cropImage.src = event.target.result;
                openCropModal();
            };
            reader.readAsDataURL(file);
        }
    });

    function openCropModal() {
        cropModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Destruir cropper anterior si existe
        if (cropper) {
            cropper.destroy();
        }
        
        // Inicializar cropper
        setTimeout(() => {
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        }, 100);
    }

    function closeCropModal() {
        cropModal.classList.add('hidden');
        document.body.style.overflow = '';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    // Cerrar modal
    document.getElementById('closeCropModal').addEventListener('click', closeCropModal);
    document.getElementById('cropModalOverlay').addEventListener('click', closeCropModal);
    document.getElementById('cancelCrop').addEventListener('click', function() {
        closeCropModal();
        photoInput.value = '';
    });

    // Controles de rotacion y zoom
    document.getElementById('rotateLeft').addEventListener('click', () => cropper && cropper.rotate(-90));
    document.getElementById('rotateRight').addEventListener('click', () => cropper && cropper.rotate(90));
    document.getElementById('zoomIn').addEventListener('click', () => cropper && cropper.zoom(0.1));
    document.getElementById('zoomOut').addEventListener('click', () => cropper && cropper.zoom(-0.1));

    // Aplicar recorte
    document.getElementById('applyCrop').addEventListener('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });
            
            croppedImageData = canvas.toDataURL('image/png');
            croppedPhotoInput.value = croppedImageData;
            
            // Mostrar preview
            croppedPreview.innerHTML = `<img src="${croppedImageData}" class="w-full h-full object-cover">`;
            newPhotoPreview.classList.remove('hidden');
            
            // Actualizar foto en el container principal
            const currentPhoto = document.getElementById('currentPhoto');
            const currentInitials = document.getElementById('currentInitials');
            
            if (currentPhoto) {
                currentPhoto.src = croppedImageData;
            } else if (currentInitials) {
                currentPhotoContainer.innerHTML = `<img src="${croppedImageData}" id="currentPhoto" class="w-full h-full object-cover">`;
            }
            
            // Actualizar header
            updateHeaderAvatar(croppedImageData);
            
            closeCropModal();
        }
    });

    // Editar recorte
    document.getElementById('editCropBtn').addEventListener('click', function() {
        openCropModal();
    });

    // Cancelar nueva foto
    document.getElementById('cancelPhotoBtn').addEventListener('click', function() {
        croppedImageData = null;
        croppedPhotoInput.value = '';
        photoInput.value = '';
        newPhotoPreview.classList.add('hidden');
        
        // Restaurar foto original
        location.reload();
    });

    // Eliminar foto
    if (deletePhotoBtn) {
        deletePhotoBtn.addEventListener('click', function() {
            if (confirm('¿Esta seguro de eliminar su foto de perfil?')) {
                fetch('{{ route("profile.photo.delete") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar la foto');
                });
            }
        });
    }

    // Funcion para actualizar avatar del header
    function updateHeaderAvatar(imageData) {
        const headerAvatar = document.querySelector('[data-header-avatar]');
        if (headerAvatar) {
            headerAvatar.innerHTML = `<img src="${imageData}" class="w-full h-full object-cover">`;
        }
    }
});
</script>
@endpush
