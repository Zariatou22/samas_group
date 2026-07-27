<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PermController extends Controller
{
    public function index(): View
    {
        return view('admin.perms.index');
    }

    public function data(): JsonResponse
    {
        $perms = Perm::withCount('groups')->orderBy('name')->get();

        $data = $perms->map(fn (Perm $p) => [
            'id' => $p->id,
            'name' => $p->name,
            'definition' => $p->definition,
            'groups_count' => $p->groups_count,
        ]);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'definition' => ['nullable', 'string'],
        ]);

        Perm::create($data);

        return back()->with('success', 'Permission enregistrée.');
    }

    public function update(Request $request, Perm $perm): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'definition' => ['nullable', 'string'],
        ]);

        $perm->update($data);

        return back()->with('success', 'Permission mise à jour.');
    }

    public function destroy(Perm $perm): RedirectResponse
    {
        $perm->delete();

        return back()->with('success', 'Permission supprimée.');
    }
}
