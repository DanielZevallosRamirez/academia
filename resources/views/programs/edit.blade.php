@extends('layouts.app')

@section('title', 'Editar Programa')
@section('page-title', 'Editar Programa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Programa</h1>
            <p class="text-gray-500">Modifica la informacion del programa academico</p>
        </div>
        <a href="{{ route('programs.show', $program) }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Volver
        </a>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('programs.update', $program) }}" enctype="multipart/form-data" 
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Info -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b">Informacion Basica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Programa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $program->name) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripcion
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('description') border-red-500 @enderror">{{ old('description', $program->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Duration & Price -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b">Duracion y Precio</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Duration Months -->
                <div>
                    <label for="duration_months" class="block text-sm font-medium text-gray-700 mb-1">
                        Duracion (meses) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="duration_months" id="duration_months" min="1" 
                           value="{{ old('duration_months', $program->duration_months) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('duration_months') border-red-500 @enderror">
                    @error('duration_months')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Hours -->
                <div>
                    <label for="total_hours" class="block text-sm font-medium text-gray-700 mb-1">
                        Horas Totales
                    </label>
                    <input type="number" name="total_hours" id="total_hours" min="1" 
                           value="{{ old('total_hours', $program->total_hours) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('total_hours') border-red-500 @enderror">
                    @error('total_hours')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                        Precio (S/) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price" id="price" min="0" step="0.01" 
                           value="{{ old('price', $program->price) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Schedule & Dates -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b">Fechas y Horario</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Inicio
                    </label>
                    <input type="date" name="start_date" id="start_date" 
                           value="{{ old('start_date', $program->start_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Fin
                    </label>
                    <input type="date" name="end_date" id="end_date" 
                           value="{{ old('end_date', $program->end_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Schedules (Multiple) -->
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Horarios
                    </label>
                    <button type="button" onclick="addScheduleField()" 
                            class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center hover:bg-emerald-200 transition-colors" title="Agregar horario">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </button>
                </div>
                <div id="schedules-container" class="space-y-2">
                    @php
                        $schedules = old('schedules');
                        if (!$schedules && $program->schedule) {
                            $decoded = json_decode($program->schedule, true);
                            $schedules = is_array($decoded) ? $decoded : [$program->schedule];
                        }
                        $schedules = $schedules ?: [''];
                    @endphp
                    @foreach($schedules as $index => $schedule)
                    <div class="schedule-row flex items-center gap-2">
                        <input type="text" name="schedules[]" 
                               value="{{ $schedule }}"
                               placeholder="Ej: Lunes 10:00 - 12:15"
                               class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @if($index > 0 || count($schedules) > 1)
                        <button type="button" onclick="removeScheduleField(this)" 
                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
                @error('schedules')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Status & Image -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b">Estado e Imagen</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                            class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('status') border-red-500 @enderror">
                        <option value="activo" @selected(old('status', $program->status) == 'activo')>Activo</option>
                        <option value="inactivo" @selected(old('status', $program->status) == 'inactivo')>Inactivo</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Imagen del Programa
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('image') border-red-500 @enderror">
                    @if($program->image)
                        <p class="text-sm text-gray-500 mt-1">Imagen actual: {{ $program->image }}</p>
                    @endif
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t">
            <a href="{{ route('programs.show', $program) }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>

    <!-- Danger Zone -->
    <div class="bg-white rounded-xl border border-red-200 p-6">
        <h3 class="text-lg font-semibold text-red-600 mb-2">Zona de Peligro</h3>
        <p class="text-gray-600 text-sm mb-4">
            Eliminar este programa removera permanentemente toda la informacion asociada, incluyendo cursos, modulos y contenidos.
        </p>
        <form method="POST" action="{{ route('programs.destroy', $program) }}" 
              onsubmit="return confirm('¿Estas seguro de eliminar este programa? Esta accion no se puede deshacer.')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                Eliminar Programa
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addScheduleField() {
    const container = document.getElementById('schedules-container');
    const newRow = document.createElement('div');
    newRow.className = 'schedule-row flex items-center gap-2';
    newRow.innerHTML = `
        <input type="text" name="schedules[]" 
               placeholder="Ej: Martes 14:00 - 16:00"
               class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <button type="button" onclick="removeScheduleField(this)" 
                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
    `;
    container.appendChild(newRow);
    lucide.createIcons();
}

function removeScheduleField(button) {
    const row = button.closest('.schedule-row');
    const container = document.getElementById('schedules-container');
    
    // Ensure at least one field remains
    if (container.querySelectorAll('.schedule-row').length > 1) {
        row.remove();
    }
}
</script>
@endpush
@endsection
