<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Perm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index');
    }

    public function data(): JsonResponse
    {
        $users = User::with('groups')->orderBy('nom')->get();

        $data = $users->map(fn (User $u) => [
            'id' => $u->id,
            'fullname' => $u->displayName(),
            'email' => $u->email,
            'groups' => $u->groups->pluck('name')->implode(', '),
            'banned' => $u->banned,
            'last_login' => optional($u->last_login)->format('d/m/Y H:i'),
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'groups' => Group::query()->orderBy('name')->get(),
            'perms' => Perm::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenoms' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:Homme,Femme'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'banned' => ['nullable', 'boolean'],
            'pass' => ['required', 'string', 'min:8'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['exists:groups,id'],
            'direct_perms' => ['nullable', 'array'],
            'direct_perms.*' => ['exists:perms,id'],
        ]);

        $groups = $data['groups'] ?? [];
        $directPerms = $data['direct_perms'] ?? [];
        unset($data['groups'], $data['direct_perms']);

        $data['banned'] = (bool) ($data['banned'] ?? false);
        $data['pass'] = Hash::make($data['pass']);

        $user = User::create($data);
        $user->groups()->sync($groups);
        $user->directPerms()->sync($directPerms);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur enregistré.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'targetUser' => $user->load(['groups', 'directPerms']),
            'groups' => Group::query()->orderBy('name')->get(),
            'perms' => Perm::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenoms' => ['required', 'string', 'max:100'],
            'sexe' => ['required', 'in:Homme,Femme'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,'.$user->id],
            'banned' => ['nullable', 'boolean'],
            'pass' => ['nullable', 'string', 'min:8'],
            'groups' => ['nullable', 'array'],
            'groups.*' => ['exists:groups,id'],
            'direct_perms' => ['nullable', 'array'],
            'direct_perms.*' => ['exists:perms,id'],
        ]);

        $groups = $data['groups'] ?? [];
        $directPerms = $data['direct_perms'] ?? [];
        unset($data['groups'], $data['direct_perms']);

        $data['banned'] = (bool) ($data['banned'] ?? false);

        if (! empty($data['pass'])) {
            $data['pass'] = Hash::make($data['pass']);
        } else {
            unset($data['pass']);
        }

        $user->update($data);
        $user->groups()->sync($groups);
        $user->directPerms()->sync($directPerms);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('success', 'Utilisateur supprimé.');
    }
}
