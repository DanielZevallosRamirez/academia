@extends('layouts.app')

@section('title', 'Mi Codigo QR')
@section('page-title', 'Mi Codigo QR')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <!-- Header -->
        <div class="w-20 h-20 bg-emerald-100 rounded-full mx-auto flex items-center justify-center mb-4">
            <i data-lucide="qr-code" class="w-10 h-10 text-emerald-600"></i>
        </div>
        <h1 class="text-xl font-bold text-gray-900">Tu Codigo QR de Asistencia</h1>
        <p class="text-gray-500 mt-1">Muestra este codigo para registrar tu asistencia</p>

        <!-- QR Code -->
        <div class="mt-8 p-6 bg-white border-4 border-gray-200 rounded-2xl inline-block">
            <div id="qrcode" class="w-64 h-64 mx-auto"></div>
        </div>

        <!-- User Info -->
        <div class="mt-6 flex items-center justify-center gap-4">
            <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
            <div class="text-left">
                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->dni ?? 'Sin DNI registrado' }}</p>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-xl">
            <div class="flex items-start gap-3">
                <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                <div class="text-left text-sm text-blue-700">
                    <p class="font-medium">Como usar tu codigo QR:</p>
                    <ol class="mt-2 space-y-1 list-decimal list-inside">
                        <li>Acercate al profesor cuando inicie la clase</li>
                        <li>Muestra este codigo QR en tu pantalla</li>
                        <li>El profesor escaneara tu codigo</li>
                        <li>Tu asistencia quedara registrada automaticamente</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-col gap-3">
            <button onclick="downloadQR()" class="w-full py-3 border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2">
                <i data-lucide="download" class="w-5 h-5"></i>
                Guardar en mi dispositivo
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qr = qrcode(0, 'M');
    qr.addData('{{ $user->qr_code }}');
    qr.make();
    document.getElementById('qrcode').innerHTML = qr.createSvgTag({ scalable: true });
});

function downloadQR() {
    const svg = document.getElementById('qrcode').querySelector('svg');
    const svgData = new XMLSerializer().serializeToString(svg);
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    img.onload = function() {
        canvas.width = 512;
        canvas.height = 512;
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        const link = document.createElement('a');
        link.download = 'mi-qr-asistencia.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    };
    
    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
}
</script>
@endpush
@endsection
