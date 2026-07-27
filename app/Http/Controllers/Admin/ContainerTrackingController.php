<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Container;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ContainerTrackingController extends Controller
{
    public function index(): View
    {
        return view('admin.container-tracking.index');
    }

    public function data(): JsonResponse
    {
        $containers = Container::query()
            ->whereNotNull('eta')
            ->whereDate('eta', '<=', now()->toDateString())
            ->with(['parentBl', 'mandataire'])
            ->orderBy('eta')
            ->get();

        $data = $containers->map(function (Container $c) {
            $daysSinceEta = (int) now()->diffInDays($c->eta);

            return [
                'id' => $c->id,
                'numero' => $c->numero,
                'bl' => $c->parentBl?->bl,
                'customer_name' => $c->mandataire?->customer_name,
                'eta' => optional($c->eta)->format('d/m/Y'),
                'days_since_eta' => $daysSinceEta,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
