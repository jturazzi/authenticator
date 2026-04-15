@extends('layouts.app')

@section('title', $totp->name . ' - Authenticator')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white mr-4">
            ← Retour
        </a>
        <h1 class="text-2xl font-bold">{{ $totp->name }}</h1>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 text-center">
        @if($totp->issuer)
            <p class="text-gray-400 mb-4">{{ $totp->issuer }}</p>
        @endif

        <div class="mb-6">
            <div class="text-4xl font-mono font-bold text-blue-400 tracking-widest totp-code"
                 data-account-id="{{ $totp->id }}" data-period="{{ $totp->period }}">
                {{ substr($code, 0, 3) }} {{ substr($code, 3) }}
            </div>
            <div class="mt-2 totp-timer inline-block" data-account-id="{{ $totp->id }}">
                <span class="countdown-text text-gray-400"></span>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-400 mb-3">QR Code</h3>
            <div class="bg-white rounded-xl p-4 inline-block">
                {!! $qrCode !!}
            </div>
            <p class="text-xs text-gray-500 mt-2">Scannez ce QR code avec une autre application authenticator</p>
        </div>

        @if($totp->description)
            <div class="text-left text-sm text-gray-300 mb-4 bg-gray-700/50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs mb-1">Description</span>
                {{ $totp->description }}
            </div>
        @endif

        <div class="text-left text-sm text-gray-400 space-y-1">
            <p><span class="text-gray-500">Algorithme:</span> {{ strtoupper($totp->algorithm) }}</p>
            <p><span class="text-gray-500">Digits:</span> {{ $totp->digits }}</p>
            <p><span class="text-gray-500">Période:</span> {{ $totp->period }}s</p>
        </div>
    </div>
</div>

<script>
function updateCode() {
    const el = document.querySelector('.totp-code');
    const period = parseInt(el.dataset.period) || 30;
    const remaining = period - (Math.floor(Date.now() / 1000) % period);
    const timerEl = document.querySelector('.countdown-text');
    timerEl.textContent = remaining + 's restantes';

    if (remaining <= 5) {
        timerEl.classList.add('text-red-400');
        timerEl.classList.remove('text-gray-400');
    } else {
        timerEl.classList.remove('text-red-400');
        timerEl.classList.add('text-gray-400');
    }

    if (remaining === period) {
        fetch(`/totp/${el.dataset.accountId}/code`)
            .then(r => r.json())
            .then(data => {
                el.textContent = data.code.substring(0, 3) + ' ' + data.code.substring(3);
            });
    }
}
setInterval(updateCode, 1000);
updateCode();
</script>
@endsection
