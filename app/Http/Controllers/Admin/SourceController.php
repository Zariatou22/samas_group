<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SourceController extends Controller
{
    public function index(): View
    {
        return view('admin.sources.index');
    }

    public function data(): JsonResponse
    {
        return response()->json(['data' => Source::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        Source::create($data + ['user' => auth()->id()]);

        return back()->with('success', "Lieu d'enlèvement enregistré.");
    }

    public function update(Request $request, Source $source): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $source->update($data);

        return back()->with('success', "Lieu d'enlèvement mis à jour.");
    }

    public function destroy(Source $source): RedirectResponse
    {
        $source->delete();

        return back()->with('success', "Lieu d'enlèvement archivé.");
    }
}
