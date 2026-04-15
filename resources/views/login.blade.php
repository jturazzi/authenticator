<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Authenticator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 rounded-xl shadow-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🔐</div>
            <h1 class="text-3xl font-bold text-white mb-2">Authenticator</h1>
            <p class="text-gray-400">Gérez vos codes d'authentification à double facteur</p>
        </div>

        <a href="{{ route('auth.microsoft') }}"
           class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 space-x-3">
            <svg class="w-6 h-6" viewBox="0 0 23 23" fill="none">
                <rect width="11" height="11" fill="#f25022"/>
                <rect x="12" width="11" height="11" fill="#7fba00"/>
                <rect y="12" width="11" height="11" fill="#00a4ef"/>
                <rect x="12" y="12" width="11" height="11" fill="#ffb900"/>
            </svg>
            <span>Se connecter avec Microsoft 365</span>
        </a>
    </div>
</body>
</html>
