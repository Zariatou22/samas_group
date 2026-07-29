<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarDriver;
use App\Models\InvoiceAdvanceLine;
use App\Models\InvoiceAdvanceReceipt;
use App\Models\Loading;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Reçus d'avance transport (document imprimable remis au chauffeur) :
 * accessible depuis Factures > Libellés > "AVANCE TRANSPORT" > Modifier,
 * plutôt que l'éditeur générique désignation/description des libellés,
 * puisqu'il s'agit d'un document métier distinct (sortie d'argent vers un
 * transporteur, pas une facture émise à un client).
 */
class InvoiceAdvanceController extends Controller
{
    public function index(): View
    {
        return view('admin.invoice-advances.index');
    }

    public function data(): JsonResponse
    {
        $receipts = InvoiceAdvanceReceipt::with(['carDriver', 'vehicle', 'parentBl'])
            ->withSum('lines', 'amount')
            ->orderByDesc('date_issued')
            ->get();

        $data = $receipts->map(fn (InvoiceAdvanceReceipt $r) => [
            'id' => $r->id,
            'reference' => $r->reference,
            'date_issued' => optional($r->date_issued)->format('d/m/Y'),
            'driver_name' => $r->carDriver?->name,
            'car' => $r->vehicle?->full_registration,
            'bl' => $r->parentBl?->bl,
            'destination' => $r->destination,
            'total' => $r->lines_sum_amount ?? 0,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.invoice-advances.form', [
            'receipt' => new InvoiceAdvanceReceipt(['date_issued' => now()]),
            'drivers' => CarDriver::with('carOwner')->orderBy('name')->get(),
            'lines' => collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateReceipt($request);
        $lines = $this->validateLines($request);

        $receipt = InvoiceAdvanceReceipt::create($data + ['user' => auth()->id()]);
        $this->syncLines($receipt, $lines);

        return redirect()->route('admin.invoice-advances.edit', $receipt)->with('success', 'Reçu d\'avance enregistré.');
    }

    public function edit(InvoiceAdvanceReceipt $invoiceAdvance): View
    {
        return view('admin.invoice-advances.form', [
            'receipt' => $invoiceAdvance,
            'drivers' => CarDriver::with('carOwner')->orderBy('name')->get(),
            'lines' => $invoiceAdvance->lines,
        ]);
    }

    public function update(Request $request, InvoiceAdvanceReceipt $invoiceAdvance): RedirectResponse
    {
        $data = $this->validateReceipt($request);
        $lines = $this->validateLines($request);

        $invoiceAdvance->update($data);
        $this->syncLines($invoiceAdvance, $lines);

        return redirect()->route('admin.invoice-advances.edit', $invoiceAdvance)->with('success', 'Reçu d\'avance mis à jour.');
    }

    public function destroy(InvoiceAdvanceReceipt $invoiceAdvance): RedirectResponse
    {
        $invoiceAdvance->delete();

        return back()->with('success', 'Reçu d\'avance archivé.');
    }

    public function print(InvoiceAdvanceReceipt $invoiceAdvance): View
    {
        return view('admin.invoice-advances.print', [
            'receipt' => $invoiceAdvance->load(['carDriver', 'vehicle', 'parentBl', 'lines']),
        ]);
    }

    /**
     * Auto-remplissage camion/BL à partir du chauffeur choisi : reprend le
     * chargement (Loading) le plus récent de ce chauffeur, car camion et BL
     * y sont déjà liés ensemble.
     */
    public function driverInfo(CarDriver $carDriver): JsonResponse
    {
        $loading = Loading::where('driver', $carDriver->id)
            ->with(['parentBl', 'vehicle'])
            ->orderByDesc('loading_date')
            ->first();

        return response()->json([
            'car' => $loading?->vehicle ? ['id' => $loading->vehicle->id, 'full_registration' => $loading->vehicle->full_registration] : null,
            'bl' => $loading?->parentBl ? ['id' => $loading->parentBl->id, 'bl' => $loading->parentBl->bl] : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateReceipt(Request $request): array
    {
        return $request->validate([
            'date_issued' => ['required', 'date'],
            'driver' => ['nullable', 'exists:car_drivers,id'],
            'car' => ['nullable', 'exists:cars,id'],
            'bl' => ['nullable', 'exists:bl,id'],
            'contact_client' => ['nullable', 'string', 'max:255'],
            'contact_transitaire' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'avance_recu' => ['nullable', 'numeric'],
            'reste_a_payer' => ['nullable', 'numeric'],
            'arrete_somme' => ['nullable', 'string', 'max:255'],
            'reste_a_payer_destination' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function validateLines(Request $request): array
    {
        $data = $request->validate([
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.designation' => ['required', 'string', 'max:255'],
            'lines.*.quantity' => ['nullable', 'numeric'],
            'lines.*.unit_price' => ['nullable', 'numeric'],
            'lines.*.amount' => ['required', 'numeric'],
        ]);

        return $data['lines'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    private function syncLines(InvoiceAdvanceReceipt $receipt, array $lines): void
    {
        $receipt->lines()->delete();

        foreach ($lines as $position => $line) {
            InvoiceAdvanceLine::create([
                'user' => auth()->id(),
                'receipt' => $receipt->id,
                'designation' => $line['designation'],
                'quantity' => $line['quantity'] ?? 1,
                'unit_price' => $line['unit_price'] ?? 0,
                'amount' => $line['amount'],
                'position' => $position,
            ]);
        }
    }
}
