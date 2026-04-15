@extends('layouts.app')

@section('title', 'Ajouter un TOTP pour ' . $user->name)

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.user.accounts', $user) }}" class="text-gray-400 hover:text-white mr-4">
            ← Retour
        </a>
        <div>
            <h1 class="text-2xl font-bold">Ajouter un compte TOTP</h1>
            <p class="text-gray-400 text-sm">Pour {{ $user->name }} ({{ $user->email }})</p>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <form method="POST" action="{{ route('admin.user.totp.store', $user) }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-1">Nom du compte *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                       placeholder="ex: user@example.com">
            </div>

            <div class="mb-4">
                <label for="issuer" class="block text-sm font-medium text-gray-300 mb-1">Émetteur</label>
                <input type="text" name="issuer" id="issuer" value="{{ old('issuer') }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                       placeholder="ex: Google, GitHub, Microsoft...">
            </div>

            <div class="mb-6">
                <label for="secret" class="block text-sm font-medium text-gray-300 mb-1">Clé secrète (optionnel)</label>
                <input type="text" name="secret" id="secret" value="{{ old('secret') }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                       placeholder="Laissez vide pour générer automatiquement">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                Ajouter le compte
            </button>
        </form>
    </div>
</div>
@endsection
