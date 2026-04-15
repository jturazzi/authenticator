@extends('layouts.app')

@section('title', 'Comptes TOTP de ' . $user->name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center">
        <a href="{{ route('admin.index') }}" class="text-gray-400 hover:text-white mr-4">
            ← Retour
        </a>
        <div>
            <h1 class="text-2xl font-bold">Comptes TOTP de {{ $user->name }}</h1>
            <p class="text-gray-400 text-sm">{{ $user->email }}</p>
        </div>
    </div>
    <a href="{{ route('admin.user.totp.create', $user) }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
        + Ajouter un compte TOTP
    </a>
</div>

@if($accounts->isEmpty())
    <div class="bg-gray-800 rounded-xl p-12 text-center">
        <div class="text-4xl mb-4">🔑</div>
        <p class="text-gray-400">Aucun compte TOTP pour cet utilisateur.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($accounts as $account)
            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-semibold text-white">{{ $account->name }}</h3>
                        @if($account->issuer)
                            <p class="text-sm text-gray-400">{{ $account->issuer }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.totp.show', $account) }}" class="text-gray-400 hover:text-blue-400" title="Détails & QR">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.totp.destroy', $account) }}"
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

                <div class="text-3xl font-mono font-bold text-blue-400 tracking-widest">
                    {{ substr($codes[$account->id], 0, 3) }} {{ substr($codes[$account->id], 3) }}
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
