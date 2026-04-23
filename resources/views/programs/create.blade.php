@extends('layouts.app')

@section('title', 'Nuevo Programa')
@section('page-title', 'Nuevo Programa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Programa</h1>
            <p class="text-gray-500">Crea un nuevo programa academico</p>
        </div>
        <a href="{{ route('programs.index') }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Volver
        </a>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('programs.store') }}" enctype="multipart/form-data" 
          class="bg-white rounded-xl border border-gray-200 p-6 space-y-6">
        @csrf

        <!-- Basic Info -->
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 pb-2 border-b">Informacion Basica</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Programa <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="Ej: Diplomado en Marketing Digital"
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
                              placeholder="Describe el programa academico..."
                              class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
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
                           value="{{ old('duration_months', 6) }}" required
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
                           value="{{ old('total_hours', 120) }}"
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
                           value="{{ old('price', 0) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
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
                        <option value="activo" @selected(old('status', 'activo') == 'activo')>Activo</option>
                        <option value="inactivo" @selected(old('status') == 'inactivo')>Inactivo</option>
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
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t">
            <a href="{{ route('programs.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                Crear Programa
            </button>
        </div>
    </form>
</div>
@endsection
