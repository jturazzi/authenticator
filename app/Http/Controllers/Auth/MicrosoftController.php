<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function callback()
    {
        $microsoftUser = Socialite::driver('microsoft')->user();

        $isFirstUser = User::count() === 0;

        $user = User::updateOrCreate(
            ['microsoft_id' => $microsoftUser->getId()],
            [
                'name' => $microsoftUser->getName(),
                'email' => $microsoftUser->getEmail(),
                'avatar' => $microsoftUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        if ($isFirstUser) {
            $user->update(['is_admin' => true]);
        }

        $user->update(['last_login_at' => now()]);

        Auth::login($user, remember: true);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
