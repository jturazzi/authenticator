<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TotpAccount;
use App\Models\User;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::withCount('totpAccounts')->orderBy('name')->paginate(20);

        return Inertia::render('Admin/Index', [
            'users' => $users,
        ]);
    }

    public function info()
    {
        return Inertia::render('Admin/Info', [
            'appVersion' => '1.0.0',
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI',
            'database' => config('database.default'),
            'cacheDriver' => config('cache.default'),
            'sessionDriver' => config('session.driver'),
            'timezone' => config('app.timezone'),
            'usersCount' => User::count(),
            'totpCount' => TotpAccount::count(),
        ]);
    }

    public function userAccounts(User $user)
    {
        $accounts = $user->totpAccounts()->latest()->get();

        $google2fa = $this->google2fa();
        $codes = [];
        foreach ($accounts as $account) {
            $codes[$account->id] = $google2fa->getCurrentOtp($account->secret);
        }

        return Inertia::render('Admin/UserAccounts', [
            'user' => $user,
            'accounts' => $accounts,
            'codes' => $codes,
        ]);
    }

    public function createAccount(User $user)
    {
        return Inertia::render('Admin/CreateAccount', [
            'user' => $user,
        ]);
    }

    public function storeAccount(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'secret' => 'nullable|string',
        ]);

        $google2fa = $this->google2fa();
        $secret = !empty($validated['secret'])
            ? strtoupper(preg_replace('/\s+/', '', $validated['secret']))
            : $google2fa->generateSecretKey();

        $user->totpAccounts()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'secret' => $secret,
        ]);

        return redirect()->route('admin.user.accounts', $user)
            ->with('success', 'flash.totpAddedFor|name:' . $user->name);
    }

    public function destroyAccount(TotpAccount $totp)
    {
        $user = $totp->user;
        $totp->delete();

        return redirect()->route('admin.user.accounts', $user)
            ->with('success', 'flash.totpDeleted');
    }

    public function toggleLock(TotpAccount $totp)
    {
        $totp->update(['locked' => !$totp->locked]);

        return redirect()->back()
            ->with('success', $totp->locked ? 'flash.totpLockedToggle' : 'flash.totpUnlockedToggle');
    }

    public function destroyUser(User $user)
    {
        if ($user->totpAccounts()->count() > 0) {
            return redirect()->back()
                ->with('error', 'flash.userDeleteError');
        }

        $user->delete();

        return redirect()->route('admin.index')
            ->with('success', 'flash.userDeleted|name:' . $user->name);
    }

    public function showAccount(TotpAccount $totp)
    {
        $google2fa = $this->google2fa();
        $code = $google2fa->getCurrentOtp($totp->secret);

        $options = new QROptions([
            'eccLevel' => EccLevel::M,
            'scale' => 5,
            'outputBase64' => false,
        ]);
        $qrCode = (new QRCode($options))->render($totp->getOtpauthUri());

        // Strip XML declaration so SVG can be embedded via v-html
        $qrCode = preg_replace('/<\?xml[^?]*\?>\s*/i', '', $qrCode);

        return Inertia::render('Admin/ShowAccount', [
            'totp' => $totp->load('user'),
            'code' => $code,
            'qrCode' => $qrCode,
        ]);
    }

    public function editAccount(TotpAccount $totp)
    {
        return Inertia::render('Admin/EditAccount', [
            'totp' => $totp->load('user')->makeVisible('secret'),
        ]);
    }

    public function updateAccount(Request $request, TotpAccount $totp)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'secret' => 'required|string',
            'digits' => 'required|integer|in:6,8',
            'period' => 'required|integer|min:10|max:120',
            'algorithm' => 'required|string|in:sha1,sha256,sha512',
        ]);

        $validated['secret'] = strtoupper(preg_replace('/\s+/', '', $validated['secret']));

        $totp->update($validated);

        return redirect()->route('admin.totp.show', $totp)
            ->with('success', 'flash.totpUpdated');
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);

        return redirect()->route('admin.index')
            ->with('success', 'flash.roleChanged|name:' . $user->name . '|role:' . ($user->is_admin ? 'flash.roleAdmin' : 'flash.roleUser'));
    }

    private function google2fa(): Google2FA
    {
        $google2fa = new Google2FA();
        $google2fa->setEnforceGoogleAuthenticatorCompatibility(false);

        return $google2fa;
    }
}
