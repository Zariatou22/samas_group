<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bl;
use App\Models\Car;
use App\Models\Container;
use App\Models\Loading;
use App\Models\LoadingContainer;
use App\Models\Source;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoadingController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.loadings.index', ['activeTab' => $request->query('activeTab', 'all')]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = match ($request->query('activeTab', 'all')) {
            'without_t1' => Loading::query()->doesntHave('t1'),
            default => Loading::query(),
        };

        $loadings = $query->with(['parentBl', 'declaration', 'mandataire', 'vehicle', 'carDriver', 't1'])
            ->orderByDesc('loading_date')
            ->get();

        $data = $loadings->map(fn (Loading $l) => [
            'id' => $l->id,
            'bl' => $l->parentBl?->bl,
            'auth_number' => $l->declaration?->auth_number,
            'customer_name' => $l->mandataire?->customer_name,
            'vehicle' => $l->vehicle?->full_registration,
            'driver_name' => $l->carDriver?->name,
            'nb_package' => $l->nb_package,
            'quantity' => $l->quantity,
            'has_t1' => $l->t1 !== null,
            'loading_date' => optional($l->loading_date)->format('d/m/Y'),
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.loadings.create', [
            'bls' => Bl::query()->orderByDesc('created')->get(),
            'cars' => Car::with(['carOwner', 'carDriver'])->orderBy('full_registration')->get(),
            'sources' => Source::query()->orderBy('name')->get(),
            'authorizationsByBl' => Bl::query()->with('authorizations')->get()
                ->mapWithKeys(fn (Bl $bl) => [$bl->id => $bl->authorizations->map(fn ($a) => ['id' => $a->id, 'auth_number' => $a->auth_number])]),
            'containersByBl' => Bl::query()->with('containers')->get()
                ->mapWithKeys(fn (Bl $bl) => [$bl->id => $bl->containers->map(fn ($c) => ['id' => $c->id, 'label' => "{$c->type_tc} — {$c->numero}"])]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bl' => ['required', 'exists:bl,id'],
            'authorization' => ['required', 'exists:authorization,id'],
            'containers' => ['nullable', 'array'],
            'containers.*' => ['exists:containers,id'],
            'nb_package' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
            'car' => ['required', 'exists:cars,id'],
            'source' => ['required', 'exists:sources,id'],
            'loading_date' => ['required', 'date'],
        ]);

        $containerIds = $data['containers'] ?? [];
        unset($data['containers']);

        $bl = Bl::findOrFail($data['bl']);
        $car = Car::findOrFail($data['car']);

        $loading = Loading::create($data + [
            'user' => auth()->id(),
            'customer' => $bl->customer,
            'customer_company' => $bl->customer_company ?? 0,
            'owner' => $car->owner,
            'driver' => $car->driver,
        ]);

        $this->syncContainers($loading, $containerIds);

        return redirect()->route('admin.loadings.index')->with('success', 'Chargement enregistré.');
    }

    public function edit(Loading $loading): View
    {
        return view('admin.loadings.edit', [
            'loading' => $loading->load(['parentBl', 'declaration', 'loadedContainers']),
            'cars' => Car::with(['carOwner', 'carDriver'])->orderBy('full_registration')->get(),
            'sources' => Source::query()->orderBy('name')->get(),
            'authorizations' => $loading->parentBl?->authorizations ?? collect(),
            'containers' => $loading->parentBl?->containers ?? collect(),
            'selectedContainerIds' => $loading->loadedContainers->pluck('container')->all(),
        ]);
    }

    public function update(Request $request, Loading $loading): RedirectResponse
    {
        $data = $request->validate([
            'authorization' => ['required', 'exists:authorization,id'],
            'containers' => ['nullable', 'array'],
            'containers.*' => ['exists:containers,id'],
            'nb_package' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
            'car' => ['required', 'exists:cars,id'],
            'source' => ['required', 'exists:sources,id'],
            'loading_date' => ['required', 'date'],
        ]);

        $containerIds = $data['containers'] ?? [];
        unset($data['containers']);

        $car = Car::findOrFail($data['car']);

        $loading->update($data + [
            'owner' => $car->owner,
            'driver' => $car->driver,
        ]);

        $this->syncContainers($loading, $containerIds);

        return redirect()->route('admin.loadings.index')->with('success', 'Chargement mis à jour.');
    }

    public function destroy(Loading $loading): RedirectResponse
    {
        if ($loading->t1 !== null) {
            return back()->with('error', 'Impossible : un T1 existe déjà pour ce chargement.');
        }

        $loading->delete();

        return back()->with('success', 'Chargement archivé.');
    }

    private function syncContainers(Loading $loading, array $containerIds): void
    {
        $existingIds = $loading->loadedContainers()->pluck('container')->all();
        $toRemove = array_diff($existingIds, $containerIds);

        if (! empty($toRemove)) {
            LoadingContainer::where('loading', $loading->id)
                ->whereIn('container', $toRemove)
                ->get()
                ->each(fn (LoadingContainer $lc) => $lc->delete());
        }

        foreach ($containerIds as $containerId) {
            $container = Container::find($containerId);
            if (! $container) {
                continue;
            }

            LoadingContainer::updateOrCreate(
                ['loading' => $loading->id, 'container' => $container->id],
                [
                    'user' => auth()->id(),
                    'customer' => $loading->customer,
                    'customer_company' => $loading->customer_company,
                    'bl' => $loading->bl,
                    'authorization' => $loading->authorization,
                    'nb_package' => $container->nb_package,
                    'quantity' => $container->quantity,
                ],
            );
        }
    }
}
