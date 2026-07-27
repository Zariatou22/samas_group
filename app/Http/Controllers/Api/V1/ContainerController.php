<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Container;
use Illuminate\Http\Request;

class ContainerController extends Controller
{
    public function getContainers(Request $request)
    {
        $query = Container::with(['parentBl', 'mandataire', 'productType']);

        if ($request->boolean('completed')) {
            $query->whereHas('parentBl', fn ($q) => $q->where('is_completed', true));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function getAvailableContainers(Request $request)
    {
        $blId = $request->integer('bl');

        if (! $blId) {
            return response()->json(['data' => []]);
        }

        $containers = Container::query()
            ->where('bl', $blId)
            ->whereDoesntHave('loadingLinks')
            ->get();

        return response()->json(['data' => $containers]);
    }

    public function getAvailableContainersForLoading(Request $request)
    {
        $blId = $request->integer('bl');
        $authId = $request->integer('authorization');
        $q = $request->input('q');
        $editIds = (array) $request->input('edit_ids', []);

        if (! $blId || ! $authId) {
            return response()->json(['items' => []]);
        }

        $items = Container::query()
            ->where('bl', $blId)
            ->where(function ($query) use ($editIds) {
                $query->whereDoesntHave('loadingLinks')
                    ->orWhereHas('loadingLinks', fn ($q) => $q->whereIn('id', $editIds));
            })
            ->when($q, fn ($query) => $query->where('numero', 'like', "%{$q}%"))
            ->get();

        return response()->json(['items' => $items]);
    }
}
