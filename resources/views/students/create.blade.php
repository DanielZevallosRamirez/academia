@extends('layouts.app')

@section('title', 'Nuevo Estudiante')
@section('page-title', 'Registrar Estudiante')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('students.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Volver a estudiantes
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Nuevo Estudiante</h2>
            <p class="text-sm text-gray-500 mt-1">Completa la informacion del estudiante</p>
        </div>

        <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
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
                    <p class="font-medium text-gray-900">Foto del estudiante</p>
                    <p class="text-sm text-gray-500">JPG o PNG. Maximo 2MB</p>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name')
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
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
                <textarea name="address" rows="2"
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('address') }}</textarea>
            </div>

            <!-- Emergency Contact -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

            <!-- Program Enrollment -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Inscripcion a Programa (Opcional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programa</label>
                        <select name="program_id"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Seleccionar programa</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }} (S/ {{ number_format($program->price, 2) }})
                                </option>
                            @endforeach
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
                <a href="{{ route('students.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Registrar Estudiante
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
</script>
@endpush
@endsection
