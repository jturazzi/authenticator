@extends('layouts.app')

@section('title', 'Ajouter un compte TOTP')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white mr-4">
            ← Retour
        </a>
        <h1 class="text-2xl font-bold">Ajouter un compte TOTP</h1>
    </div>

    <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
        <form method="POST" action="{{ route('totp.store') }}">
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

            <div class="mb-4">
                <label for="secret" class="block text-sm font-medium text-gray-300 mb-1">Clé secrète (optionnel)</label>
                <input type="text" name="secret" id="secret" value="{{ old('secret') }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                       placeholder="Laissez vide pour générer automatiquement">
                <p class="text-xs text-gray-500 mt-1">Si vous avez une clé secrète, collez-la ici. Sinon, une sera générée.</p>
            </div>

            <div class="my-6 border-t border-gray-700"></div>

            <div class="mb-4">
                <label for="otpauth_uri" class="block text-sm font-medium text-gray-300 mb-1">Ou collez une URI otpauth://</label>
                <input type="text" name="otpauth_uri" id="otpauth_uri" value="{{ old('otpauth_uri') }}"
                       class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white font-mono text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                       placeholder="otpauth://totp/...">
                <p class="text-xs text-gray-500 mt-1">Si vous collez une URI, les champs ci-dessus seront ignorés.</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                Ajouter le compte
            </button>
        </form>
    </div>
</div>
@endsection
