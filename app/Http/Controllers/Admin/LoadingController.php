<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bl;
use App\Models\Car;
use App\Models\Container;
use App\Models\DeliveryNote;
use App\Models\Loading;
use App\Models\LoadingContainer;
use App\Models\Source;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Chargements (type=0) et dépotages (type=1) partagent le même formulaire et
 * la même logique — seule la disponibilité (BL/déclarations/conteneurs) est
 * comptée séparément par type, comme Loading::show_loadings_list()/
 * save_loading_form() côté CodeIgniter (Ajout de sous menu dépotage).
 */
class LoadingController extends Controller
{
    public function index(Request $request): View
    {
        return $this->indexView($request, Loading::TYPE_LOADING, 'Chargements', 'admin.loadings.data');
    }

    public function data(Request $request): JsonResponse
    {
        return $this->dataJson($request, Loading::TYPE_LOADING);
    }

    public function unloadingIndex(Request $request): View
    {
        return $this->indexView($request, Loading::TYPE_UNLOADING, 'Dépotages', 'admin.loadings.unloadings-data');
    }

    public function unloadingData(Request $request): JsonResponse
    {
        return $this->dataJson($request, Loading::TYPE_UNLOADING);
    }

    public function create(): View
    {
        return $this->createView(Loading::TYPE_LOADING);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->storeRequest($request, Loading::TYPE_LOADING);
    }

    public function unloadingCreate(): View
    {
        return $this->createView(Loading::TYPE_UNLOADING);
    }

    public function unloadingStore(Request $request): RedirectResponse
    {
        return $this->storeRequest($request, Loading::TYPE_UNLOADING);
    }

    public function edit(Loading $loading): View
    {
        $isUnloading = $loading->type === Loading::TYPE_UNLOADING;

        return view('admin.loadings.edit', [
            'loading' => $loading->load(['parentBl.deliveryNote', 'declaration', 'loadedContainers']),
            'isUnloading' => $isUnloading,
            'listRoute' => $isUnloading ? 'admin.loadings.unloadings' : 'admin.loadings.index',
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
            'bad_valid_date' => ['nullable', 'date'],
        ]);

        $containerIds = $data['containers'] ?? [];
        $badValidDate = $data['bad_valid_date'] ?? null;
        unset($data['containers'], $data['bad_valid_date']);

        $car = Car::findOrFail($data['car']);

        $loading->update($data + [
            'owner' => $car->owner,
            'driver' => $car->driver,
        ]);

        $this->syncContainers($loading, $containerIds);
        $this->syncBadValidDate($loading->bl, $badValidDate);

        $isUnloading = $loading->type === Loading::TYPE_UNLOADING;

        return redirect()->route($isUnloading ? 'admin.loadings.unloadings' : 'admin.loadings.index')
            ->with('success', $isUnloading ? 'Dépotage mis à jour.' : 'Chargement mis à jour.');
    }

    public function destroy(Loading $loading): RedirectResponse
    {
        $isUnloading = $loading->type === Loading::TYPE_UNLOADING;

        if ($loading->t1 !== null) {
            return back()->with('error', 'Impossible : un T1 existe déjà pour '.($isUnloading ? 'ce dépotage' : 'ce chargement').'.');
        }

        $loading->delete();

        return back()->with('success', $isUnloading ? 'Dépotage archivé.' : 'Chargement archivé.');
    }

    private function indexView(Request $request, int $type, string $title, string $dataRoute): View
    {
        return view('admin.loadings.index', [
            'title' => $title,
            'isUnloading' => $type === Loading::TYPE_UNLOADING,
            'activeTab' => $request->query('activeTab', 'all'),
            'dataRoute' => $dataRoute,
        ]);
    }

    private function dataJson(Request $request, int $type): JsonResponse
    {
        $query = match ($request->query('activeTab', 'all')) {
            'without_t1' => Loading::query()->doesntHave('t1'),
            default => Loading::query(),
        };

        $loadings = $query->ofType($type)
            ->with(['parentBl.deliveryNote', 'declaration', 'mandataire', 'vehicle', 'carDriver', 't1'])
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
            'bad_valid_date' => optional($l->parentBl?->deliveryNote?->date_valid)->format('d/m/Y'),
        ]);

        return response()->json(['data' => $data]);
    }

    private function createView(int $type): View
    {
        $isUnloading = $type === Loading::TYPE_UNLOADING;

        return view('admin.loadings.create', [
            'isUnloading' => $isUnloading,
            'storeRoute' => $isUnloading ? 'admin.loadings.unloadings.store' : 'admin.loadings.store',
            'listRoute' => $isUnloading ? 'admin.loadings.unloadings' : 'admin.loadings.index',
            'bls' => $this->availableBlsForLoading($type),
            'cars' => Car::with(['carOwner', 'carDriver'])->orderBy('full_registration')->get(),
            'sources' => Source::query()->orderBy('name')->get(),
            'authorizationsByBl' => Bl::query()->with('authorizations')->get()
                ->mapWithKeys(fn (Bl $bl) => [$bl->id => $bl->authorizations->map(fn ($a) => ['id' => $a->id, 'auth_number' => $a->auth_number])]),
            'containersByBl' => Bl::query()->with('containers')->get()
                ->mapWithKeys(fn (Bl $bl) => [$bl->id => $bl->containers->map(fn ($c) => ['id' => $c->id, 'label' => "{$c->type_tc} — {$c->numero}"])]),
        ]);
    }

    private function storeRequest(Request $request, int $type): RedirectResponse
    {
        $isUnloading = $type === Loading::TYPE_UNLOADING;

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
            'bad_valid_date' => ['nullable', 'date'],
        ]);

        $containerIds = $data['containers'] ?? [];
        $badValidDate = $data['bad_valid_date'] ?? null;
        unset($data['containers'], $data['bad_valid_date']);

        $bl = Bl::findOrFail($data['bl']);

        if ($bl->availableForLoadingType($type) <= 0) {
            $message = $isUnloading
                ? 'Ce BL ne dispose plus de capacité de dépotage résiduelle.'
                : 'Ce BL ne dispose plus de capacité de chargement résiduelle.';

            return back()->withInput()->withErrors(['bl' => $message]);
        }

        $car = Car::findOrFail($data['car']);

        $loading = Loading::create($data + [
            'user' => auth()->id(),
            'type' => $type,
            'customer' => $bl->customer,
            'customer_company' => $bl->customer_company ?? 0,
            'owner' => $car->owner,
            'driver' => $car->driver,
        ]);

        $this->syncContainers($loading, $containerIds);
        $this->syncBadValidDate($bl->id, $badValidDate);

        return redirect()->route($isUnloading ? 'admin.loadings.unloadings' : 'admin.loadings.index')
            ->with('success', $isUnloading ? 'Le dépotage a été enregistré avec succès.' : 'Le chargement a été enregistré avec succès.');
    }

    /**
     * BL proposables : au moins une déclaration active et une capacité
     * résiduelle pour CE type (loaded < quantité déclarée), comme
     * Loads::get_available_bl($type) côté CodeIgniter.
     */
    private function availableBlsForLoading(int $type)
    {
        return Bl::query()
            ->whereHas('authorizations')
            ->with('deliveryNote')
            ->orderByDesc('created')
            ->get()
            ->filter(fn (Bl $bl) => $bl->availableForLoadingType($type) > 0)
            ->values();
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

    /**
     * Met à jour uniquement la date de validité du BAD du BL (saisie depuis
     * le formulaire de chargement/dépotage), sans toucher à la date de
     * réception si elle existe déjà — reprend Bls::set_bad_valid_date().
     */
    private function syncBadValidDate(int $blId, ?string $validDate): void
    {
        if (empty($validDate)) {
            return;
        }

        DeliveryNote::updateOrCreate(
            ['bl' => $blId],
            ['user' => auth()->id(), 'date_valid' => $validDate],
        );
    }
}
