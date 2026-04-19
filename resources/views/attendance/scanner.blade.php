@extends('layouts.app')

@section('title', 'Escaner de Asistencia')
@section('page-title', 'Registrar Asistencia')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Session Info -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $session->title }}</h1>
                <p class="text-gray-500">{{ $session->course->name }} - {{ $session->course->program->name }}</p>
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                        {{ $session->session_date->format('d/m/Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        {{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}
                    </span>
                </div>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-medium
                {{ $session->status === 'en_curso' ? 'bg-green-100 text-green-700' : '' }}
                {{ $session->status === 'programada' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $session->status === 'finalizada' ? 'bg-gray-100 text-gray-700' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $session->status)) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- QR Scanner -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Escaner QR</h2>
                <button id="toggleCamera" class="px-3 py-1 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                    Iniciar Camara
                </button>
            </div>
            
            <div class="aspect-square bg-gray-900 relative">
                <video id="preview" class="w-full h-full object-cover"></video>
                <div id="scanner-overlay" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-64 h-64 border-4 border-white/50 rounded-2xl relative">
                        <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-emerald-500 rounded-tl-lg"></div>
                        <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-emerald-500 rounded-tr-lg"></div>
                        <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-emerald-500 rounded-bl-lg"></div>
                        <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-emerald-500 rounded-br-lg"></div>
                    </div>
                </div>
                <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white">
                    <i data-lucide="camera" class="w-16 h-16 text-gray-400 mb-4"></i>
                    <p class="text-gray-400">Presiona "Iniciar Camara" para escanear</p>
                </div>
            </div>

            <!-- Manual Input -->
            <div class="p-4 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-2">O ingresa el codigo manualmente:</p>
                <div class="flex gap-2">
                    <input type="text" id="manual-code" placeholder="Codigo QR del estudiante"
                           class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <button onclick="submitManualCode()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                        Registrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Attendance List -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 border-b border-gray-200">
                <h2 class="font-semibold text-gray-900">Lista de Asistencia</h2>
                @php $stats = $session->getAttendanceStats(); @endphp
                <div class="flex items-center gap-4 mt-2 text-sm">
                    <span class="text-green-600">{{ $stats['presente'] }} presentes</span>
                    <span class="text-red-600">{{ $stats['ausente'] }} ausentes</span>
                    <span class="text-yellow-600">{{ $stats['tardanza'] }} tardanzas</span>
                </div>
            </div>
            
            <div id="attendance-list" class="divide-y divide-gray-100 max-h-[500px] overflow-y-auto">
                @foreach($session->attendances as $attendance)
                    <div class="p-4 flex items-center gap-4" id="attendance-{{ $attendance->id }}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $attendance->status_badge }}">
                            @if($attendance->status === 'presente')
                                <i data-lucide="check" class="w-5 h-5"></i>
                            @elseif($attendance->status === 'ausente')
                                <i data-lucide="x" class="w-5 h-5"></i>
                            @elseif($attendance->status === 'tardanza')
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            @else
                                <i data-lucide="file-text" class="w-5 h-5"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate">{{ $attendance->student->name }}</p>
                            <p class="text-sm text-gray-500">
                                @if($attendance->check_in_time)
                                    {{ $attendance->check_in_time->format('H:i') }}
                                @else
                                    Sin registro
                                @endif
                            </p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $attendance->status_badge }}">
                            {{ $attendance->status_label }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Scans -->
    <div id="recent-scans" class="mt-6 space-y-2"></div>
</div>

@push('scripts')
<script src="https://rawgit.com/AcademicoMDP/html5-qrcode/master/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let isScanning = false;

document.getElementById('toggleCamera').addEventListener('click', function() {
    if (isScanning) {
        stopScanner();
    } else {
        startScanner();
    }
});

function startScanner() {
    html5QrCode = new Html5Qrcode("preview");
    
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanFailure
    ).then(() => {
        isScanning = true;
        document.getElementById('toggleCamera').textContent = 'Detener Camara';
        document.getElementById('camera-placeholder').style.display = 'none';
    }).catch(err => {
        console.error('Error starting camera:', err);
        alert('No se pudo acceder a la camara. Verifica los permisos.');
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            document.getElementById('toggleCamera').textContent = 'Iniciar Camara';
            document.getElementById('camera-placeholder').style.display = 'flex';
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    registerAttendance(decodedText);
}

function onScanFailure(error) {
    // Ignore scan failures
}

function submitManualCode() {
    const code = document.getElementById('manual-code').value.trim();
    if (code) {
        registerAttendance(code);
        document.getElementById('manual-code').value = '';
    }
}

function registerAttendance(qrCode) {
    fetch('{{ route("attendance.scan", $session) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ qr_code: qrCode })
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success);
        
        if (data.success && data.student) {
            addRecentScan(data.student);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al registrar asistencia', false);
    });
}

function showNotification(message, success) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-xl shadow-lg z-50 flex items-center gap-3 ${
        success ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.innerHTML = `
        <i data-lucide="${success ? 'check-circle' : 'alert-circle'}" class="w-6 h-6"></i>
        <span class="font-medium">${message}</span>
    `;
    document.body.appendChild(notification);
    lucide.createIcons();
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function addRecentScan(student) {
    const container = document.getElementById('recent-scans');
    const scanDiv = document.createElement('div');
    scanDiv.className = 'bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-4 animate-pulse';
    scanDiv.innerHTML = `
        <img src="${student.photo}" class="w-12 h-12 rounded-full object-cover">
        <div class="flex-1">
            <p class="font-medium text-green-900">${student.name}</p>
            <p class="text-sm text-green-700">Registrado como ${student.status}</p>
        </div>
        <span class="text-sm text-green-600">${new Date().toLocaleTimeString()}</span>
    `;
    container.prepend(scanDiv);
    
    // Remove animation after a moment
    setTimeout(() => {
        scanDiv.classList.remove('animate-pulse');
    }, 1000);
    
    // Keep only last 5 scans
    while (container.children.length > 5) {
        container.lastChild.remove();
    }
}

// Handle Enter key on manual input
document.getElementById('manual-code').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        submitManualCode();
    }
});
</script>
@endpush
@endsection
