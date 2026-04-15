@extends('layouts.app')

@section('title', 'Administration - Authenticator')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Administration des utilisateurs</h1>
    <div class="flex space-x-3 text-xs text-gray-400">
        <span class="bg-gray-800 border border-gray-700 px-3 py-1 rounded">Laravel {{ app()->version() }}</span>
        <span class="bg-gray-800 border border-gray-700 px-3 py-1 rounded">PHP {{ PHP_VERSION }}</span>
    </div>
</div>

<div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-700">
            <tr>
                <th class="text-left px-6 py-3 text-sm font-medium text-gray-300">Utilisateur</th>
                <th class="text-left px-6 py-3 text-sm font-medium text-gray-300">Email</th>
                <th class="text-center px-6 py-3 text-sm font-medium text-gray-300">Comptes TOTP</th>
                <th class="text-center px-6 py-3 text-sm font-medium text-gray-300">Rôle</th>
                <th class="text-right px-6 py-3 text-sm font-medium text-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @foreach($users as $user)
                <tr class="hover:bg-gray-750">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" alt="" class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="font-medium text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-blue-900 text-blue-300 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $user->totp_accounts_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->is_admin)
                            <span class="bg-red-900 text-red-300 text-xs font-medium px-2.5 py-0.5 rounded">Admin</span>
                        @else
                            <span class="bg-gray-700 text-gray-300 text-xs font-medium px-2.5 py-0.5 rounded">Utilisateur</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('admin.user.accounts', $user) }}"
                           class="text-blue-400 hover:text-blue-300 text-sm">Voir comptes</a>

                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.user.toggle-admin', $user) }}" class="inline"
                                  onsubmit="return confirm('Changer le rôle de {{ $user->name }} ?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-yellow-400 hover:text-yellow-300 text-sm">
                                    {{ $user->is_admin ? 'Retirer admin' : 'Rendre admin' }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
