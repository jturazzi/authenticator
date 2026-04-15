@extends('layouts.app')

@section('title', $totp->name . ' - Admin')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.user.accounts', $totp->user) }}" class="text-gray-400 hover:text-white mr-4">
            ← Retour
        </a>
        <div>
            <h1 class="text-2xl font-bold">{{ $totp->name }}</h1>
            <p class="text-gray-400 text-sm">Utilisateur: {{ $totp->user->name }}</p>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 text-center">
        @if($totp->issuer)
            <p class="text-gray-400 mb-4">{{ $totp->issuer }}</p>
        @endif

        <div class="mb-6">
            <div class="text-4xl font-mono font-bold text-blue-400 tracking-widest">
                {{ substr($code, 0, 3) }} {{ substr($code, 3) }}
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-400 mb-3">QR Code</h3>
            <div class="bg-white rounded-xl p-4 inline-block">
                {!! $qrCode !!}
            </div>
            <p class="text-xs text-gray-500 mt-2">Scannez ce QR code avec une application authenticator</p>
        </div>

        <div class="text-left text-sm text-gray-400 space-y-1">
            <p><span class="text-gray-500">Algorithme:</span> {{ strtoupper($totp->algorithm) }}</p>
            <p><span class="text-gray-500">Digits:</span> {{ $totp->digits }}</p>
            <p><span class="text-gray-500">Période:</span> {{ $totp->period }}s</p>
        </div>
    </div>
</div>
@endsection
