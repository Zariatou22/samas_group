<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Mot de passe oublié par email avec code de sécurité, comme
 * Account::lost_password()/check_code()/reset_password() en CI —
 * simplifié en un flux linéaire à 3 étapes (email → code → nouveau mot de
 * passe) plutôt que les multiples chemins redondants de l'existant CI.
 */
class ForgotPasswordController extends Controller
{
    public function showRequest(): View
    {
        return view('auth.forgot-password');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $user = User::query()->where('email', $data['email'])->first();

        if ($user) {
            $code = (string) random_int(100000, 999999);

            $user->forceFill([
                'verification_code' => $code,
                'forgot_exp' => now()->addMinutes(30)->toDateTimeString(),
            ])->save();

            Mail::raw("Votre code de sécurité pour réinitialiser votre mot de passe : {$code}\nCe code expire dans 30 minutes.", function ($message) use ($user) {
                $message->to($user->email)->subject('Réinitialisation de mot de passe - SAMAS Groupe');
            });
        }

        return redirect()->route('password.verify.show', ['email' => $data['email']])
            ->with('success', 'Si cet email existe, un code de sécurité vient de lui être envoyé.');
    }

    public function showVerify(Request $request): View
    {
        return view('auth.verify-code', ['email' => $request->query('email')]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        $valid = $user
            && $user->verification_code === $data['code']
            && $user->forgot_exp
            && now()->lt($user->forgot_exp);

        if (! $valid) {
            return back()->withInput()->withErrors(['code' => 'Code incorrect ou expiré.']);
        }

        $token = Str::random(40);
        $request->session()->put('password_reset', ['user_id' => $user->id, 'token' => $token]);

        return redirect()->route('password.reset.show', ['token' => $token]);
    }

    public function showReset(Request $request): View|RedirectResponse
    {
        $reset = $request->session()->get('password_reset');

        if (! $reset || $reset['token'] !== $request->query('token')) {
            return redirect()->route('login');
        }

        return view('auth.reset-password', ['token' => $reset['token']]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $reset = $request->session()->get('password_reset');

        $data = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! $reset || $reset['token'] !== $data['token']) {
            return redirect()->route('login')->withErrors(['email' => 'Session de réinitialisation expirée, recommencez.']);
        }

        $user = User::findOrFail($reset['user_id']);
        $user->forceFill([
            'pass' => Hash::make($data['password']),
            'verification_code' => null,
            'forgot_exp' => null,
        ])->save();

        $request->session()->forget('password_reset');

        return redirect()->route('login')->with('success', 'Mot de passe mis à jour, vous pouvez vous connecter.');
    }
}
