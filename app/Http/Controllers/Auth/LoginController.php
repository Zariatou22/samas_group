<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.home');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Identifiants incorrects.']);
        }

        if (auth()->user()->banned) {
            Auth::logout();

            return back()->withErrors(['email' => 'Ce compte est désactivé.']);
        }

        $request->session()->regenerate();

        $this->logConnection($request, 'login');

        return redirect()->intended(route('admin.home'));
    }

    /**
     * Journal des connexions, comme Auth::sethistory() en CI.
     */
    private function logConnection(Request $request, string $action): void
    {
        $agent = (string) $request->userAgent();

        $platform = match (true) {
            str_contains($agent, 'Windows') => 'Windows',
            str_contains($agent, 'Mac OS') => 'Mac OS',
            str_contains($agent, 'Android') => 'Android',
            str_contains($agent, 'iPhone'), str_contains($agent, 'iPad') => 'iOS',
            str_contains($agent, 'Linux') => 'Linux',
            default => 'Inconnu',
        };

        $browser = match (true) {
            str_contains($agent, 'Edg/') => 'Edge',
            str_contains($agent, 'Chrome/') => 'Chrome',
            str_contains($agent, 'Firefox/') => 'Firefox',
            str_contains($agent, 'Safari/') && ! str_contains($agent, 'Chrome') => 'Safari',
            default => 'Inconnu',
        };

        $device = preg_match('/Mobi|Android|iPhone|iPad/i', $agent) ? 'Mobile' : 'PC';

        UserAction::create([
            'user' => auth()->id(),
            'action' => $action,
            'platform' => $platform,
            'device' => $device,
            'browser' => $browser,
            'ip' => $request->ip(),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
