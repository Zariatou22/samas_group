<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loading;
use App\Models\LoadingT1;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoadingT1Controller extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.loading-t1s.index', [
            'activeTab' => $request->query('activeTab', 'ongoing'),
            'counts' => [
                'total' => LoadingT1::query()->count(),
                'ongoing' => LoadingT1::query()->ongoing()->count(),
                'expired' => LoadingT1::query()->expired()->count(),
                'waiting' => Loading::query()->doesntHave('t1')->count(),
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        if ($request->query('activeTab', 'ongoing') === 'waiting') {
            $loadings = Loading::query()->doesntHave('t1')
                ->with(['parentBl', 'mandataire', 'vehicle'])
                ->orderByDesc('loading_date')
                ->get();

            $data = $loadings->map(fn (Loading $l) => [
                'id' => $l->id,
                'is_loading_without_t1' => true,
                'bl' => $l->parentBl?->bl,
                'customer_name' => $l->mandataire?->customer_name,
                'vehicle' => $l->vehicle?->full_registration,
            ]);

            return response()->json(['data' => $data]);
        }

        $query = match ($request->query('activeTab', 'ongoing')) {
            'expired' => LoadingT1::query()->expired(),
            default => LoadingT1::query()->ongoing(),
        };

        $t1s = $query->with(['parentLoading.parentBl', 'parentLoading.mandataire', 'parentLoading.vehicle'])
            ->orderByDesc('created')
            ->get();

        $data = $t1s->map(fn (LoadingT1 $t1) => [
            'id' => $t1->id,
            't1_number' => $t1->t1_number,
            'bl' => $t1->parentLoading?->parentBl?->bl,
            'customer_name' => $t1->parentLoading?->mandataire?->customer_name,
            'vehicle' => $t1->parentLoading?->vehicle?->full_registration,
            'valid_until' => optional($t1->valid_until)->format('d/m/Y'),
            'is_validated' => $t1->isValidated(),
            'validate' => optional($t1->validate)->format('d/m/Y'),
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.loading-t1s.create', [
            'loadings' => Loading::query()->doesntHave('t1')->with(['parentBl', 'mandataire', 'vehicle'])->orderByDesc('created')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'loading' => ['required', 'exists:loading,id'],
            't1_number' => ['required', 'numeric'],
            'valid_until' => ['required', 'date'],
        ]);

        LoadingT1::create($data + [
            'user' => auth()->id(),
        ]);

        return redirect()->route('admin.loading-t1s.index')->with('success', 'T1 enregistré.');
    }

    public function edit(LoadingT1 $loadingT1): View
    {
        return view('admin.loading-t1s.edit', [
            'loadingT1' => $loadingT1->load(['parentLoading.parentBl', 'parentLoading.mandataire', 'parentLoading.vehicle']),
        ]);
    }

    public function update(Request $request, LoadingT1 $loadingT1): RedirectResponse
    {
        $data = $request->validate([
            't1_number' => ['required', 'numeric'],
            'valid_until' => ['required', 'date'],
            'validate' => ['nullable', 'date'],
        ]);

        $loadingT1->update($data);

        return redirect()->route('admin.loading-t1s.index')->with('success', 'T1 mis à jour.');
    }

    public function validateT1(LoadingT1 $loadingT1): RedirectResponse
    {
        $loadingT1->update(['validate' => now()]);

        return back()->with('success', 'T1 validé.');
    }

    /**
     * Validation en masse : sélection multiple de T1 + une date de
     * validation appliquée à tous, comme T1::validate()/
     * TransitT1::validate_t1() en CI.
     */
    public function validateBulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['exists:loading_t1,id'],
            'validate_date' => ['required', 'date'],
        ]);

        LoadingT1::query()->whereIn('id', $data['ids'])->update(['validate' => $data['validate_date']]);

        return back()->with('success', count($data['ids']).' T1 validé(s).');
    }

    public function destroy(LoadingT1 $loadingT1): RedirectResponse
    {
        $loadingT1->delete();

        return back()->with('success', 'T1 archivé.');
    }
}
