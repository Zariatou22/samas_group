<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Bl;
use App\Models\Car;
use App\Models\Container;
use App\Models\Loading;
use App\Models\LoadingContainer;
use Illuminate\Http\Request;

class LoadingController extends Controller
{
    public function getLoadings()
    {
        $loadings = Loading::with([
            'mandataire', 'customerCompany', 'parentBl', 'declaration',
            'vehicle', 'carOwner', 'carDriver', 'pickupSource', 'loadedContainers', 't1',
        ])->get();

        return response()->json(['data' => $loadings]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $bl = Bl::findOrFail($data['bl']);
        $car = Car::findOrFail($data['car']);

        $loading = Loading::create([
            ...$data,
            'user' => $request->user()->id,
            'customer' => $bl->customer,
            'customer_company' => $bl->customer_company ?? 0,
            'owner' => $car->owner,
            'driver' => $car->driver,
        ]);

        $this->syncContainers($loading, $data['containers'] ?? []);

        return response()->json(['success' => true, 'message' => $loading->fresh(['parentBl', 'vehicle', 'declaration'])]);
    }

    public function update(Request $request, Loading $loading)
    {
        $data = $this->validated($request);

        $bl = Bl::findOrFail($data['bl']);
        $car = Car::findOrFail($data['car']);

        $loading->update([
            ...$data,
            'customer' => $bl->customer,
            'customer_company' => $bl->customer_company ?? 0,
            'owner' => $car->owner,
            'driver' => $car->driver,
        ]);

        $this->syncContainers($loading, $data['containers'] ?? []);

        return response()->json(['success' => true, 'message' => $loading->fresh(['parentBl', 'vehicle', 'declaration'])]);
    }

    private function syncContainers(Loading $loading, array $containerIds): void
    {
        $existingIds = $loading->loadedContainers()->pluck('container')->all();
        $toRemove = array_diff($existingIds, $containerIds);

        if (! empty($toRemove)) {
            LoadingContainer::where('loading', $loading->id)->whereIn('container', $toRemove)->get()
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
                    'user' => $loading->user,
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

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'bl' => ['required', 'integer', 'exists:bl,id'],
            'authorization' => ['required', 'integer', 'exists:authorization,id'],
            'nb_package' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'car' => ['required', 'integer', 'exists:cars,id'],
            'source' => ['required', 'integer', 'exists:sources,id'],
            'loading_date' => ['required', 'date'],
            'containers' => ['sometimes', 'array'],
            'containers.*' => ['integer', 'exists:containers,id'],
        ]);

        return $data;
    }
}
