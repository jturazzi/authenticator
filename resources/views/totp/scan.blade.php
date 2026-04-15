@extends('layouts.app')

@section('title', 'Scanner un QR Code')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white mr-4">
            ← Retour
        </a>
        <h1 class="text-2xl font-bold">Scanner un QR Code</h1>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        {{-- Scanner --}}
        <div id="scanner-container">
            <div class="bg-gray-900 rounded-lg overflow-hidden aspect-square relative">
                <video id="scanner-video" class="w-full h-full object-cover" playsinline></video>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-48 h-48 border-2 border-blue-400 rounded-xl opacity-50"></div>
                </div>
            </div>
            <div class="mt-3 flex justify-center space-x-3">
                <button id="btn-start" onclick="startScanner()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    📷 Démarrer la caméra
                </button>
                <button id="btn-stop" onclick="stopScanner()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition hidden">
                    ⏹ Arrêter
                </button>
            </div>
        </div>

        {{-- Formulaire après scan --}}
        <form method="POST" action="{{ route('totp.scan.store') }}" id="scan-form" class="hidden">
            @csrf
            <input type="hidden" name="otpauth_uri" id="otpauth_uri">
            <div class="bg-green-900/30 border border-green-700 rounded-lg p-3 mb-4">
                <p class="text-green-400 text-sm font-medium">✓ QR code détecté</p>
                <p id="scan-label" class="text-gray-300 text-sm mt-1"></p>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Nom à donner au compte</label>
                <input type="text" name="description" id="description"
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                       placeholder="Ex: GitHub perso, AWS prod..." required autofocus>
                @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                Ajouter ce compte
            </button>
        </form>
    </div>
</div>

<script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let videoStream = null;
let scanInterval = null;

function playScanSound() {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.type = 'sine';
    osc.frequency.setValueAtTime(1200, ctx.currentTime);
    osc.frequency.setValueAtTime(1600, ctx.currentTime + 0.08);
    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
    osc.start(ctx.currentTime);
    osc.stop(ctx.currentTime + 0.2);
}

function onQrDetected(data) {
    stopScanner();
    playScanSound();

    document.getElementById('otpauth_uri').value = data;

    try {
        const url = new URL(data);
        const label = decodeURIComponent(url.pathname.replace(/^\//, ''));
        document.getElementById('scan-label').textContent = label;
    } catch (e) {
        document.getElementById('scan-label').textContent = data;
    }

    document.getElementById('scanner-container').classList.add('hidden');
    document.getElementById('scan-form').classList.remove('hidden');
    document.getElementById('description').focus();
}

async function startScanner() {
    const video = document.getElementById('scanner-video');
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }
        });
        video.srcObject = videoStream;
        await video.play();

        document.getElementById('btn-start').classList.add('hidden');
        document.getElementById('btn-stop').classList.remove('hidden');

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        scanInterval = setInterval(() => {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                const result = jsQR(imageData.data, imageData.width, imageData.height);
                if (result && result.data.startsWith('otpauth://')) {
                    onQrDetected(result.data);
                }
            }
        }, 300);
    } catch (err) {
        alert('Impossible d\'accéder à la caméra: ' + err.message);
    }
}

function stopScanner() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
    if (scanInterval) {
        clearInterval(scanInterval);
        scanInterval = null;
    }
    document.getElementById('btn-start').classList.remove('hidden');
    document.getElementById('btn-stop').classList.add('hidden');
}
</script>
@endsection
