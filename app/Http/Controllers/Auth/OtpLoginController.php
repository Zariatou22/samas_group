<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemVariable;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

/**
 * Connexion "méthode 2" avec code OTP, comme
 * Account::login_otp()/get_otp()/otp()/check_otp() en CI : un secret TOTP
 * unique partagé par toute l'application (stocké dans system_variables),
 * pas un secret par utilisateur — reproduit tel quel, cette simplicité
 * étant la conception d'origine plutôt qu'une limitation à corriger ici.
 */
class OtpLoginController extends Controller
{
    private function tfa(): TwoFactorAuth
    {
        // Format SVG : rendu sans dépendance à l'extension imagick.
        return new TwoFactorAuth(new BaconQrCodeProvider(format: 'svg'), 'SAMAS Groupe');
    }

    public function show(): View
    {
        return view('auth.login-otp');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
            'code' => ['required', 'string'],
        ]);

        $secret = SystemVariable::get('otp');

        if (! $secret || ! $this->tfa()->verifyCode($secret, $credentials['code'])) {
            return back()->withInput()->withErrors(['code' => 'Le code est incorrect.']);
        }

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            return back()->withInput()->withErrors(['email' => 'Identifiants incorrects.']);
        }

        if (auth()->user()->banned) {
            Auth::logout();

            return back()->withErrors(['email' => 'Ce compte est désactivé.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.home'));
    }

    public function showSetup(): View
    {
        return view('auth.login-otp-setup');
    }

    /**
     * Régénère le secret TOTP partagé et affiche son QR code à scanner —
     * comme Account::get_otp()/otp() en CI, chaque configuration invalide
     * la précédente puisque le secret est unique pour toute l'application.
     */
    public function setup(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->getAuthPassword())) {
            return back()->withInput()->withErrors(['email' => 'Identifiants incorrects.']);
        }

        $tfa = $this->tfa();
        $secret = $tfa->createSecret();
        SystemVariable::put('otp', $secret);

        $qrCode = $tfa->getQRCodeImageAsDataUri($user->email, $secret);

        return view('auth.login-otp-qrcode', ['qrCode' => $qrCode]);
    }
}
