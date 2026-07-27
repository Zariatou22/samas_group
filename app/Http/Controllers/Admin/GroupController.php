<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Perm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index(): View
    {
        return view('admin.groups.index');
    }

    public function data(): JsonResponse
    {
        $groups = Group::withCount('users')->with('perms')->orderBy('name')->get();

        $data = $groups->map(fn (Group $g) => [
            'id' => $g->id,
            'name' => $g->name,
            'definition' => $g->definition,
            'perms' => $g->perms->pluck('name')->implode(', '),
            'users_count' => $g->users_count,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.groups.create', [
            'perms' => Perm::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'definition' => ['nullable', 'string'],
            'perms' => ['nullable', 'array'],
            'perms.*' => ['exists:perms,id'],
        ]);

        $perms = $data['perms'] ?? [];
        unset($data['perms']);

        $group = Group::create($data);
        $group->perms()->sync($perms);

        return redirect()->route('admin.groups.index')->with('success', 'Groupe enregistré.');
    }

    public function edit(Group $group): View
    {
        return view('admin.groups.edit', [
            'group' => $group->load('perms'),
            'perms' => Perm::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'definition' => ['nullable', 'string'],
            'perms' => ['nullable', 'array'],
            'perms.*' => ['exists:perms,id'],
        ]);

        $perms = $data['perms'] ?? [];
        unset($data['perms']);

        $group->update($data);
        $group->perms()->sync($perms);

        return redirect()->route('admin.groups.index')->with('success', 'Groupe mis à jour.');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $group->delete();

        return back()->with('success', 'Groupe supprimé.');
    }
}
