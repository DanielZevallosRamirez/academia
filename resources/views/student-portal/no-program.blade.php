@extends('layouts.app')

@section('title', 'Sin Programa')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center">
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center max-w-md">
        <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900">No tienes un programa activo</h2>
        <p class="text-gray-500 mt-2">Actualmente no estas inscrito en ningun programa academico.</p>
        <p class="text-sm text-gray-400 mt-4">Contacta a administracion para inscribirte en un programa.</p>
        
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 mt-6 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Dashboard
        </a>
    </div>
</div>
@endsection
