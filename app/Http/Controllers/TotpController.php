<?php

namespace App\Http\Controllers;

use App\Models\TotpAccount;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PragmaRX\Google2FA\Google2FA;

class TotpController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()->totpAccounts()->latest()->get();

        $google2fa = $this->google2fa();
        $codes = [];

        foreach ($accounts as $account) {
            $codes[$account->id] = $google2fa->getCurrentOtp($account->secret);
        }

        return Inertia::render('Dashboard', [
            'accounts' => $accounts,
            'codes' => $codes,
        ]);
    }

    public function create()
    {
        return Inertia::render('Totp/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'issuer' => 'nullable|string|max:255',
            'secret' => 'nullable|string',
            'otpauth_uri' => 'nullable|string',
        ]);

        $google2fa = $this->google2fa();

        if (!empty($validated['otpauth_uri'])) {
            $parsed = $this->parseOtpauthUri($validated['otpauth_uri']);
            if (!$parsed) {
                return back()->withErrors(['otpauth_uri' => 'URI otpauth invalide.'])->withInput();
            }

            $account = Auth::user()->totpAccounts()->create($parsed);
        } else {
            $secret = !empty($validated['secret'])
                ? strtoupper(preg_replace('/\s+/', '', $validated['secret']))
                : $google2fa->generateSecretKey();

            $account = Auth::user()->totpAccounts()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'issuer' => $validated['issuer'],
                'secret' => $secret,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Compte TOTP ajouté avec succès.');
    }

    public function show(TotpAccount $totp)
    {
        $this->authorizeAccess($totp);

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

        return Inertia::render('Totp/Show', [
            'totp' => $totp,
            'code' => $code,
            'qrCode' => $qrCode,
        ]);
    }

    public function destroy(TotpAccount $totp)
    {
        $this->authorizeAccess($totp);

        if ($totp->locked) {
            return redirect()->route('dashboard')
                ->with('error', 'Ce compte TOTP est verrouillé et ne peut pas être supprimé.');
        }

        $totp->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Compte TOTP supprimé.');
    }

    public function getCode(TotpAccount $totp)
    {
        $this->authorizeAccess($totp);

        $google2fa = $this->google2fa();
        $code = $google2fa->getCurrentOtp($totp->secret);
        $remainingSeconds = $totp->period - (time() % $totp->period);

        return response()->json([
            'code' => $code,
            'remaining' => $remainingSeconds,
            'period' => $totp->period,
        ]);
    }

    public function scan()
    {
        return Inertia::render('Totp/Scan');
    }

    public function storeScan(Request $request)
    {
        $validated = $request->validate([
            'otpauth_uri' => 'required|string',
            'description' => 'required|string|max:1000',
        ]);

        $parsed = $this->parseOtpauthUri($validated['otpauth_uri']);
        if (!$parsed) {
            return back()->withErrors(['otpauth_uri' => 'QR code invalide.'])->withInput();
        }

        if (!empty($validated['description'])) {
            $parsed['description'] = $validated['description'];
        }

        Auth::user()->totpAccounts()->create($parsed);

        return redirect()->route('dashboard')
            ->with('success', 'Compte TOTP ajouté via QR code.');
    }

    private function parseOtpauthUri(string $uri): ?array
    {
        if (!str_starts_with($uri, 'otpauth://totp/')) {
            return null;
        }

        $parsed = parse_url($uri);
        if (!$parsed || !isset($parsed['path'])) {
            return null;
        }

        $label = urldecode(ltrim($parsed['path'], '/'));
        parse_str($parsed['query'] ?? '', $params);

        if (empty($params['secret'])) {
            return null;
        }

        $issuer = $params['issuer'] ?? null;
        $name = $label;

        if (str_contains($label, ':')) {
            [$issuer, $name] = explode(':', $label, 2);
            $issuer = trim($issuer);
            $name = trim($name);
        }

        return [
            'name' => $name,
            'issuer' => $issuer ?? $params['issuer'] ?? null,
            'secret' => strtoupper($params['secret']),
            'digits' => (int) ($params['digits'] ?? 6),
            'period' => (int) ($params['period'] ?? 30),
            'algorithm' => strtolower($params['algorithm'] ?? 'sha1'),
        ];
    }

    private function authorizeAccess(TotpAccount $totp): void
    {
        if ($totp->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
    }

    private function google2fa(): Google2FA
    {
        $google2fa = new Google2FA();
        $google2fa->setEnforceGoogleAuthenticatorCompatibility(false);

        return $google2fa;
    }
}
