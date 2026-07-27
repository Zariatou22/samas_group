<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemVariable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    private const GENERAL_KEYS = [
        'title', 'keywords', 'description', 'adresse', 'email', 'contact',
        'facebook', 'twitter', 'instagram', 'linkedin', 'youtube',
        'maintenance',
    ];

    private const EMAIL_KEYS = [
        'email-smtp-server', 'email-smtp-auth', 'email-smtp-port',
        'email-smtp-username', 'email-smtp-password',
        'email-sender-name', 'email-reply-name',
        'email-sender-email', 'email-reply-email', 'email-is-html',
    ];

    public function general(): View
    {
        $values = [];
        foreach (self::GENERAL_KEYS as $key) {
            $values[$key] = SystemVariable::get($key, '');
        }
        $values['maintenance'] = SystemVariable::get('maintenance', '0') === '1';

        return view('admin.settings.general', ['values' => $values]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'contact' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'keywords' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'maintenance' => ['nullable', 'boolean'],
        ]);

        foreach (self::GENERAL_KEYS as $key) {
            $value = $data[$key] ?? '';
            if ($key === 'maintenance') {
                $value = $value ? '1' : '0';
            }
            SystemVariable::put($key, $value);
        }

        return back()->with('success', 'Paramètres enregistrés.');
    }

    public function email(): View
    {
        $values = [];
        foreach (self::EMAIL_KEYS as $key) {
            $values[$key] = SystemVariable::get($key, '');
        }
        $values['email-smtp-auth'] = SystemVariable::get('email-smtp-auth', '0') === '1';
        $values['email-is-html'] = SystemVariable::get('email-is-html', '0') === '1';

        return view('admin.settings.email', ['values' => $values]);
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email-smtp-server' => ['nullable', 'string', 'max:255'],
            'email-smtp-port' => ['nullable', 'numeric'],
            'email-smtp-username' => ['nullable', 'string', 'max:255'],
            'email-smtp-password' => ['nullable', 'string', 'max:255'],
            'email-smtp-auth' => ['nullable', 'boolean'],
            'email-sender-name' => ['nullable', 'string', 'max:255'],
            'email-sender-email' => ['nullable', 'email'],
            'email-reply-name' => ['nullable', 'string', 'max:255'],
            'email-reply-email' => ['nullable', 'email'],
            'email-is-html' => ['nullable', 'boolean'],
        ]);

        foreach (self::EMAIL_KEYS as $key) {
            $value = $data[$key] ?? '';
            if (in_array($key, ['email-smtp-auth', 'email-is-html'], true)) {
                $value = $value ? '1' : '0';
            }
            SystemVariable::put($key, $value);
        }

        return back()->with('success', 'Paramètres email enregistrés.');
    }
}
