@extends('layouts.app')

@section('title', 'Dashboard - Authenticator')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Mes codes d'authentification</h1>
    <div class="flex space-x-3">
        <a href="{{ route('totp.scan') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm transition">
            📷 Scanner QR Code
        </a>
        <a href="{{ route('totp.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
            + Ajouter manuellement
        </a>
    </div>
</div>

@if($accounts->isEmpty())
    <div class="bg-gray-800 rounded-xl p-12 text-center">
        <div class="text-6xl mb-4">🔑</div>
        <h2 class="text-xl font-semibold mb-2">Aucun compte TOTP</h2>
        <p class="text-gray-400 mb-6">Ajoutez votre premier compte d'authentification à double facteur.</p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('totp.scan') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition">
                📷 Scanner un QR Code
            </a>
            <a href="{{ route('totp.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
                + Ajouter manuellement
            </a>
        </div>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($accounts as $account)
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 hover:border-gray-600 transition"
                 id="account-{{ $account->id }}">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-white">{{ $account->description ?? $account->name }}</h3>
                        @if($account->issuer)
                            <p class="text-sm text-gray-400">{{ $account->issuer }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('totp.show', $account) }}" class="text-gray-400 hover:text-blue-400" title="Détails">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('totp.destroy', $account) }}"
                              onsubmit="return confirm('Supprimer ce compte TOTP ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-400" title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="totp-code text-3xl font-mono font-bold text-blue-400 tracking-widest"
                         data-account-id="{{ $account->id }}"
                         data-period="{{ $account->period }}">
                        {{ substr($codes[$account->id], 0, 3) }} {{ substr($codes[$account->id], 3) }}
                    </div>
                    <div class="totp-timer relative w-10 h-10" data-account-id="{{ $account->id }}">
                        <svg class="w-10 h-10 transform -rotate-90" viewBox="0 0 40 40">
                            <circle cx="20" cy="20" r="18" stroke="#374151" stroke-width="3" fill="none"/>
                            <circle class="countdown-circle" cx="20" cy="20" r="18"
                                    stroke="#3B82F6" stroke-width="3" fill="none"
                                    stroke-dasharray="113"
                                    stroke-dashoffset="0"
                                    stroke-linecap="round"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-xs font-bold countdown-text">
                            --
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
function updateAllCodes() {
    document.querySelectorAll('.totp-code').forEach(el => {
        const accountId = el.dataset.accountId;
        const period = parseInt(el.dataset.period) || 30;
        const remaining = period - (Math.floor(Date.now() / 1000) % period);

        // Update timer
        const timerEl = document.querySelector(`.totp-timer[data-account-id="${accountId}"]`);
        if (timerEl) {
            const circle = timerEl.querySelector('.countdown-circle');
            const text = timerEl.querySelector('.countdown-text');
            const offset = 113 * (1 - remaining / period);
            circle.style.strokeDashoffset = offset;
            text.textContent = remaining;

            if (remaining <= 5) {
                circle.style.stroke = '#EF4444';
            } else {
                circle.style.stroke = '#3B82F6';
            }
        }

        // Refresh codes when timer resets
        if (remaining === period) {
            fetch(`/totp/${accountId}/code`)
                .then(r => r.json())
                .then(data => {
                    const code = data.code;
                    el.textContent = code.substring(0, 3) + ' ' + code.substring(3);
                });
        }
    });
}

setInterval(updateAllCodes, 1000);
updateAllCodes();
</script>
@endsection
