@extends('layouts.app')

@section('title', 'Codigo QR - ' . $user->name)
@section('page-title', 'Codigo QR del Usuario')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('users.show', $user) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Volver al perfil
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <!-- User Info -->
        <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" 
             class="w-20 h-20 rounded-full mx-auto object-cover mb-4">
        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }} {{ $user->last_name }}</h2>
        <p class="text-gray-500">{{ $user->email }}</p>

        <!-- QR Code -->
        <div class="mt-8 p-4 bg-white border-4 border-gray-200 rounded-2xl inline-block">
            <div id="qrcode" class="w-64 h-64 mx-auto"></div>
        </div>

        <p class="mt-4 text-sm text-gray-500">
            Codigo: <span class="font-mono">{{ $user->qr_code }}</span>
        </p>

        <!-- Actions -->
        <div class="mt-8 flex flex-col gap-3">
            <button onclick="printQR()" class="w-full py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 flex items-center justify-center gap-2">
                <i data-lucide="printer" class="w-5 h-5"></i>
                Imprimir QR
            </button>
            <button onclick="downloadQR()" class="w-full py-3 border border-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2">
                <i data-lucide="download" class="w-5 h-5"></i>
                Descargar QR
            </button>
            <form method="POST" action="{{ route('users.regenerate-qr', $user) }}" 
                  onsubmit="return confirm('¿Estas seguro? El codigo anterior dejara de funcionar.')">
                @csrf
                <button type="submit" class="w-full py-3 text-red-600 font-medium rounded-lg hover:bg-red-50 flex items-center justify-center gap-2">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    Regenerar Codigo QR
                </button>
            </form>
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

function printQR() {
    window.print();
}

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
        link.download = 'qr-{{ $user->dni ?? $user->id }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    };
    
    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
}
</script>
@endpush

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #qrcode, #qrcode * { visibility: visible; }
    #qrcode { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }
}
</style>
@endpush
@endsection
