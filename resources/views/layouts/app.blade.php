<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authenticator')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes countdown {
            from { stroke-dashoffset: 0; }
            to { stroke-dashoffset: 113; }
        }
    </style>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-400">
                        🔐 Authenticator
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">
                            Mes comptes
                        </a>
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.index') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">
                                Administration
                            </a>
                        @endif
                    @endauth
                </div>
                @auth
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full">
                            @endif
                            <span class="text-sm text-gray-300">{{ auth()->user()->name }}</span>
                            @if(auth()->user()->is_admin)
                                <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded">Admin</span>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-red-400 text-sm">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-800 border border-green-600 text-green-200 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-800 border border-red-600 text-red-200 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
