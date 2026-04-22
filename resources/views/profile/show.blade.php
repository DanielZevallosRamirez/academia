@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('header', 'Mi Perfil')

@push('styles')
<style>
    #qrcode canvas {
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Mensajes de exito/error --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl">
            <div class="flex items-center gap-3 mb-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="font-medium">Modo desarrollo - Link de verificacion:</span>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" value="{{ session('warning') }}" readonly class="flex-1 text-xs bg-amber-100 p-2 rounded-lg font-mono border-0 focus:ring-2 focus:ring-amber-300" id="verificationLink">
                <button onclick="copyLink()" class="px-3 py-2 bg-amber-200 hover:bg-amber-300 text-amber-800 rounded-lg text-xs font-medium transition-colors">
                    Copiar
                </button>
                <a href="{{ session('warning') }}" class="px-3 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-medium transition-colors">
                    Verificar Ahora
                </a>
            </div>
        </div>
        <script>
            function copyLink() {
                const input = document.getElementById('verificationLink');
                input.select();
                document.execCommand('copy');
                alert('Link copiado al portapapeles');
            }
        </script>
    @endif

    {{-- Tarjeta Principal del Perfil --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Banner --}}
        <div class="h-32 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>
        
        {{-- Info del usuario --}}
        <div class="relative px-6 pb-6">
            {{-- Avatar --}}
            <div class="absolute -top-16 left-6">
                <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-lg overflow-hidden bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center">
                    @if($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}</span>
                    @endif
                </div>
            </div>

            {{-- Boton editar --}}
            <div class="flex justify-end pt-4">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Editar Perfil
                </a>
            </div>

            {{-- Nombre y rol --}}
            <div class="mt-8">
                <h2 class="text-2xl font-bold text-slate-800">{{ $user->name }} {{ $user->last_name }}</h2>
                <div class="flex items-center gap-3 mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($user->role === 'admin') bg-red-100 text-red-700
                        @elseif($user->role === 'profesor') bg-violet-100 text-violet-700
                        @else bg-blue-100 text-blue-700 @endif">
                        @if($user->role === 'admin')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Administrador
                        @elseif($user->role === 'profesor')
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                            Profesor
                        @else
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Estudiante
                        @endif
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($user->status === 'activo') bg-emerald-100 text-emerald-700
                        @elseif($user->status === 'inactivo') bg-slate-100 text-slate-700
                        @else bg-amber-100 text-amber-700 @endif">
                        <span class="w-2 h-2 rounded-full mr-2 
                            @if($user->status === 'activo') bg-emerald-500
                            @elseif($user->status === 'inactivo') bg-slate-400
                            @else bg-amber-500 @endif"></span>
                        {{ ucfirst($user->status ?? 'activo') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid de informacion --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Informacion Personal --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                Informacion Personal
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Nombre completo</span>
                    <span class="font-medium text-slate-800">{{ $user->name }} {{ $user->last_name }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">DNI / Documento</span>
                    <span class="font-medium text-slate-800">{{ $user->dni ?? 'No registrado' }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Fecha de nacimiento</span>
                    <span class="font-medium text-slate-800">
                        {{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') : 'No registrada' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-slate-500">Direccion</span>
                    <span class="font-medium text-slate-800 text-right max-w-[200px]">{{ $user->address ?? 'No registrada' }}</span>
                </div>
            </div>
        </div>

        {{-- Informacion de Contacto --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                Informacion de Contacto
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Correo electronico</span>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-800">{{ $user->email }}</span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center justify-center w-5 h-5 bg-emerald-100 rounded-full" title="Verificado">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                        @else
                            <form action="{{ route('verification.send') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors" title="Enviar enlace de verificacion">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Verificar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Telefono</span>
                    <span class="font-medium text-slate-800">{{ $user->phone ?? 'No registrado' }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Contacto de emergencia</span>
                    <span class="font-medium text-slate-800">{{ $user->emergency_contact ?? 'No registrado' }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-slate-500">Tel. emergencia</span>
                    <span class="font-medium text-slate-800">{{ $user->emergency_phone ?? 'No registrado' }}</span>
                </div>
            </div>
        </div>

        {{-- Codigo QR --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                Mi Codigo QR
            </h3>
            <div class="flex flex-col items-center">
                @if($user->qr_code)
                    <div class="bg-white p-4 rounded-xl border-2 border-slate-200 mb-4">
                        <div id="qrcode" class="w-48 h-48 flex items-center justify-center"></div>
                    </div>
                    <p class="text-sm text-slate-500 text-center mb-3">Este codigo QR es unico para ti. Usalo para registrar tu asistencia.</p>
                    <button onclick="downloadQR()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-violet-700 bg-violet-100 hover:bg-violet-200 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar QR
                    </button>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <p class="text-slate-500">No tienes un codigo QR asignado</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Informacion de la Cuenta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                Informacion de la Cuenta
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Miembro desde</span>
                    <span class="font-medium text-slate-800">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Ultima actualizacion</span>
                    <span class="font-medium text-slate-800">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-slate-500">Email verificado</span>
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center text-emerald-600 font-medium">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $user->email_verified_at->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="inline-flex items-center text-amber-600 font-medium">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Pendiente
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- QRCode.js library --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    @if($user->qr_code)
    // Generar el codigo QR
    document.addEventListener('DOMContentLoaded', function() {
        const qrContainer = document.getElementById('qrcode');
        if (qrContainer) {
            new QRCode(qrContainer, {
                text: "{{ $user->qr_code }}",
                width: 192,
                height: 192,
                colorDark: "#1e293b",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });

    // Funcion para descargar el QR
    function downloadQR() {
        const canvas = document.querySelector('#qrcode canvas');
        if (canvas) {
            const link = document.createElement('a');
            link.download = 'mi-codigo-qr.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        }
    }
    @endif
</script>
@endpush
