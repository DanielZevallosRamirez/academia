@extends('layouts.app')

@php
    $roleNames = ['admin' => 'Administrador', 'profesor' => 'Profesor', 'estudiante' => 'Estudiante'];
    $roleColors = ['admin' => 'purple', 'profesor' => 'blue', 'estudiante' => 'emerald'];
@endphp

@section('title', 'Nuevo ' . $roleNames[$defaultRole])
@section('page-title', 'Registrar Usuario')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Volver a usuarios
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Nuevo Usuario</h2>
            <p class="text-sm text-gray-500 mt-1">Completa la informacion del usuario</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="p-6 space-y-6" x-data="{ role: '{{ $defaultRole }}' }">
            @csrf

            <!-- Photo -->
            <div class="flex items-center gap-6">
                <div class="relative">
                    <div id="photo-preview" class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                        <i data-lucide="user" class="w-12 h-12 text-gray-400"></i>
                    </div>
                    <label class="absolute bottom-0 right-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-emerald-700">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                    </label>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Foto del usuario</p>
                    <p class="text-sm text-gray-500">JPG o PNG. Maximo 2MB</p>
                </div>
            </div>

            <!-- Role Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Usuario *</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="estudiante" x-model="role" class="peer sr-only" {{ $defaultRole === 'estudiante' ? 'checked' : '' }}>
                        <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-gray-300">
                            <i data-lucide="graduation-cap" class="w-6 h-6 mx-auto mb-1 text-emerald-600"></i>
                            <p class="font-medium text-gray-900 text-sm">Estudiante</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="profesor" x-model="role" class="peer sr-only" {{ $defaultRole === 'profesor' ? 'checked' : '' }}>
                        <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-gray-300">
                            <i data-lucide="book-open" class="w-6 h-6 mx-auto mb-1 text-blue-600"></i>
                            <p class="font-medium text-gray-900 text-sm">Profesor</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="secretario" x-model="role" class="peer sr-only" {{ $defaultRole === 'secretario' ? 'checked' : '' }}>
                        <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 hover:border-gray-300">
                            <i data-lucide="clipboard-list" class="w-6 h-6 mx-auto mb-1 text-amber-600"></i>
                            <p class="font-medium text-gray-900 text-sm">Secretario(a)</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="administrativo" x-model="role" class="peer sr-only" {{ $defaultRole === 'administrativo' ? 'checked' : '' }}>
                        <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-cyan-500 peer-checked:bg-cyan-50 hover:border-gray-300">
                            <i data-lucide="briefcase" class="w-6 h-6 mx-auto mb-1 text-cyan-600"></i>
                            <p class="font-medium text-gray-900 text-sm">Administrativo</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="role" value="admin" x-model="role" class="peer sr-only" {{ $defaultRole === 'admin' ? 'checked' : '' }}>
                        <div class="p-3 border-2 border-gray-200 rounded-xl text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-gray-300">
                            <i data-lucide="shield" class="w-6 h-6 mx-auto mb-1 text-purple-600"></i>
                            <p class="font-medium text-gray-900 text-sm">Administrador</p>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contrasena</label>
                    <input type="password" name="password"
                           placeholder="Dejar vacio para generar automaticamente"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                    <input type="text" name="dni" value="{{ old('dni') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('dni') border-red-500 @enderror">
                    @error('dni')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <!-- Specialty (only for professors) -->
                <div x-show="role === 'profesor'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                    <input type="text" name="specialty" value="{{ old('specialty') }}"
                           placeholder="Ej: Marketing Digital, Desarrollo Web..."
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
                <textarea name="address" rows="2"
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('address') }}</textarea>
            </div>

            <!-- Emergency Contact (for students) -->
            <div x-show="role === 'estudiante'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contacto de emergencia</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefono de emergencia</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Program Enrollment (only for students) -->
            <div x-show="role === 'estudiante'" x-transition class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Inscripcion a Programa (Opcional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programa</label>
                        <select name="program_id" id="program_select"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Seleccionar programa</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" data-price="{{ $program->price }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }} (S/ {{ number_format($program->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de pago</label>
                        <select name="payment_type" id="payment_type"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="contado" {{ old('payment_type') === 'contado' ? 'selected' : '' }}>Pago al contado</option>
                            <option value="cuotas" {{ old('payment_type') === 'cuotas' ? 'selected' : '' }}>Pago en cuotas</option>
                        </select>
                    </div>
                    <div id="installments_container" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numero de cuotas</label>
                        <select name="num_installments"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @for($i = 2; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('num_installments') == $i ? 'selected' : '' }}>{{ $i }} cuotas</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('users.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Registrar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photo-preview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const paymentType = document.getElementById('payment_type');
    const installmentsContainer = document.getElementById('installments_container');
    
    if (paymentType) {
        paymentType.addEventListener('change', function() {
            installmentsContainer.style.display = this.value === 'cuotas' ? 'block' : 'none';
        });
        
        // Initial state
        if (paymentType.value === 'cuotas') {
            installmentsContainer.style.display = 'block';
        }
    }
});
</script>
@endpush
@endsection
