<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bl;
use App\Models\Container;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'stats' => [
                ['label' => "En attente d'arrivée", 'description' => 'Conteneurs pas encore arrivés', 'count' => Bl::waiting()->count(), 'color' => 'secondary', 'url' => route('admin.bls.index', ['activeTab' => 'waiting'])],
                ['label' => "En attente d'opération", 'description' => "Arrivés, opération pas démarrée", 'count' => Bl::arrived()->count(), 'color' => 'warning', 'url' => route('admin.bls.index', ['activeTab' => 'arrived'])],
                ['label' => "En cours d'opération", 'description' => 'Chargement en cours', 'count' => Bl::ongoing()->count(), 'color' => 'info', 'url' => route('admin.bls.index', ['activeTab' => 'ongoing'])],
                ['label' => 'Opérations clôturées', 'description' => 'Terminées', 'count' => Bl::completed()->count(), 'color' => 'success', 'url' => route('admin.bls.index', ['activeTab' => 'completed'])],
            ],
        ]);
    }

    public function containersPerMonth(): JsonResponse
    {
        $counts = Container::query()
            ->selectRaw('MONTH(eta) as month, COUNT(*) as total')
            ->whereYear('eta', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $data[] = (int) ($counts[$month] ?? 0);
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    public function weekArrivals(): JsonResponse
    {
        $bls = Bl::query()
            ->whereHas('containers', fn ($q) => $q->whereBetween('eta', [now()->toDateString(), now()->addDays(7)->toDateString()]))
            ->with(['mandataire', 'shippingCompany', 'containers', 'exchange', 'deliveryNote'])
            ->orderBy('eta_date')
            ->get();

        $data = $bls->map(fn (Bl $bl) => [
            'id' => $bl->id,
            'bl' => $bl->bl,
            'customer_name' => $bl->mandataire?->customer_name,
            'containers_count' => $bl->containers->count(),
            'etas' => $bl->containers->pluck('eta')->filter()->map(fn ($eta) => $eta->format('d/m/Y'))->implode('<br>'),
            'company_name' => $bl->shippingCompany?->name,
            'description' => $bl->description,
            'type_operation' => $bl->type_operation,
            'has_exchange' => $bl->exchange !== null,
            'bad_date' => optional($bl->deliveryNote?->date_received)->format('d/m/Y'),
            'valid_date' => optional($bl->deliveryNote?->date_valid)->format('d/m/Y'),
            'observation' => $bl->observation,
        ]);

        return response()->json(['data' => $data]);
    }
}
