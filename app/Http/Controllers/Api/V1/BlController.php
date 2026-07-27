<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Bl;
use Illuminate\Http\Request;

class BlController extends Controller
{
    public function listBls(Request $request)
    {
        $query = Bl::query()->with(['mandataire', 'customerCompany', 'shippingCompany', 'productType']);

        if ($request->filled('customer')) {
            $query->where('customer', $request->input('customer'));
        }
        if ($request->filled('customer_company')) {
            $query->where('customer_company', $request->input('customer_company'));
        }
        if ($request->filled('company')) {
            $query->where('company', $request->input('company'));
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('eta_date', [$request->input('start_date'), $request->input('end_date')]);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function containers(Request $request)
    {
        $id = $request->integer('id');

        if (! $id) {
            return response()->json(['data' => []]);
        }

        $bl = Bl::find($id);

        return response()->json(['data' => $bl?->containers()->with('productType')->get() ?? []]);
    }

    public function operations(Request $request)
    {
        $id = $request->integer('id');
        $bl = $id ? Bl::find($id) : null;

        if (! $bl) {
            return response()->json(['data' => []]);
        }

        return response()->json(['data' => [
            'is_started' => (bool) $bl->is_started,
            'is_completed' => (bool) $bl->is_completed,
            'authorized_quantity' => $bl->authorized_quantity,
            'available_quantity' => $bl->available_quantity,
        ]]);
    }

    public function getAvailable()
    {
        $bls = Bl::query()
            ->whereHas('authorizations')
            ->with(['mandataire', 'customerCompany', 'shippingCompany'])
            ->get()
            ->filter(fn (Bl $bl) => $bl->available_quantity > 0)
            ->values();

        return response()->json(['data' => $bls]);
    }

    public function getWaitingWeek()
    {
        $bls = Bl::query()
            ->whereHas('containers', fn ($q) => $q->whereBetween('eta', [now()->startOfWeek()->toDateString(), now()->startOfWeek()->addDays(7)->toDateString()]))
            ->with(['mandataire', 'customerCompany', 'shippingCompany', 'exchange', 'deliveryNote', 'containers'])
            ->get();

        return response()->json(['data' => $bls]);
    }

    public function setObservation(Request $request)
    {
        $data = $request->validate([
            'bl_id' => ['required', 'integer'],
            'observation' => ['nullable', 'string'],
        ]);

        $bl = Bl::find($data['bl_id']);

        if (! $bl) {
            return response()->json(['success' => false, 'message' => 'BL introuvable']);
        }

        $bl->update(['observation' => $data['observation'] ?? '']);

        return response()->json(['success' => true, 'message' => 'ok']);
    }
}
