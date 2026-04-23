@extends('layouts.app')

@section('title', 'Editar Estudiante')
@section('page-title', 'Editar Estudiante')

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
            <h2 class="text-xl font-semibold text-gray-900">Editar Estudiante</h2>
            <p class="text-sm text-gray-500 mt-1">Actualiza la informacion del estudiante</p>
        </div>

        <form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Photo -->
            <div class="flex items-center gap-6">
                <div class="relative">
                    <div id="photo-preview" class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($student->photo)
                            <img src="{{ Storage::url($student->photo) }}" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="user" class="w-12 h-12 text-gray-400"></i>
                        @endif
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" value="{{ old('name', $student->name) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI</label>
                    <input type="text" name="dni" value="{{ old('dni', $student->dni) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('dni') border-red-500 @enderror">
                    @error('dni')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="activo" @selected(old('status', $student->status) == 'activo')>Activo</option>
                    <option value="inactivo" @selected(old('status', $student->status) == 'inactivo')>Inactivo</option>
                    <option value="suspendido" @selected(old('status', $student->status) == 'suspendido')>Suspendido</option>
                </select>
            </div>

            <!-- Address -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Direccion</label>
                <textarea name="address" rows="2"
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('address', $student->address) }}</textarea>
            </div>

            <!-- Emergency Contact -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contacto de emergencia</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $student->emergency_contact) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefono de emergencia</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $student->emergency_phone) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Current Enrollments -->
            @if($student->enrollments->count() > 0)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Programas Inscritos</h3>
                <div class="space-y-3">
                    @foreach($student->enrollments as $enrollment)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $enrollment->program->name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $enrollment->start_date?->format('d/m/Y') }} - {{ $enrollment->end_date?->format('d/m/Y') ?? 'Sin fecha fin' }}
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            {{ $enrollment->status === 'activo' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $enrollment->status === 'completado' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $enrollment->status === 'cancelado' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $enrollment->status === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Add New Enrollment -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Agregar Nueva Inscripcion (Opcional)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programa</label>
                        <select name="program_id" id="program_id" onchange="loadProgramData(this.value)"
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Seleccionar programa</option>
                            @foreach($programs as $program)
                                @php
                                    $schedules = [];
                                    if ($program->schedule) {
                                        $decoded = json_decode($program->schedule, true);
                                        $schedules = is_array($decoded) ? $decoded : [$program->schedule];
                                    }
                                @endphp
                                <option value="{{ $program->id }}" 
                                        data-start-date="{{ $program->start_date?->format('Y-m-d') }}"
                                        data-end-date="{{ $program->end_date?->format('Y-m-d') }}"
                                        data-schedule="{{ implode(' | ', $schedules) }}"
                                        {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }} (S/ {{ number_format($program->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                        <input type="date" name="enrollment_start_date" id="enrollment_start_date" value="{{ old('enrollment_start_date') }}"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
                        <input type="date" name="enrollment_end_date" id="enrollment_end_date" value="{{ old('enrollment_end_date') }}"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horario(s) del programa</label>
                        <div id="program_schedule" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 min-h-[42px]">
                            <span class="text-gray-400 italic">Seleccione un programa para ver el horario</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <button type="button" onclick="confirmDelete()" class="px-4 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                    Eliminar estudiante
                </button>
                <div class="flex items-center gap-4">
                    <a href="{{ route('students.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" action="{{ route('students.destroy', $student) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

function confirmDelete() {
    Swal.fire({
        title: 'Eliminar Estudiante',
        html: '<p class="text-gray-600">Estas seguro de eliminar a <strong>{{ $student->name }} {{ $student->last_name }}</strong>?</p><p class="text-red-500 text-sm mt-2">Esta accion no se puede deshacer.</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form').submit();
        }
    });
}

function loadProgramData(programId) {
    const select = document.getElementById('program_id');
    const selectedOption = select.options[select.selectedIndex];
    
    const startDateInput = document.getElementById('enrollment_start_date');
    const endDateInput = document.getElementById('enrollment_end_date');
    const scheduleDiv = document.getElementById('program_schedule');
    
    if (programId) {
        const startDate = selectedOption.dataset.startDate;
        const endDate = selectedOption.dataset.endDate;
        const schedule = selectedOption.dataset.schedule;
        
        startDateInput.value = startDate || '';
        endDateInput.value = endDate || '';
        
        if (schedule) {
            const schedules = schedule.split(' | ');
            if (schedules.length > 1) {
                scheduleDiv.innerHTML = '<ul class="list-disc list-inside">' + 
                    schedules.map(s => `<li>${s}</li>`).join('') + '</ul>';
            } else {
                scheduleDiv.innerHTML = `<span>${schedule}</span>`;
            }
        } else {
            scheduleDiv.innerHTML = '<span class="text-gray-400 italic">No especificado</span>';
        }
    } else {
        startDateInput.value = '';
        endDateInput.value = '';
        scheduleDiv.innerHTML = '<span class="text-gray-400 italic">Seleccione un programa para ver el horario</span>';
    }
}

// Initialize on page load if program is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    const programSelect = document.getElementById('program_id');
    if (programSelect && programSelect.value) {
        loadProgramData(programSelect.value);
    }
});
</script>
@endpush
@endsection
