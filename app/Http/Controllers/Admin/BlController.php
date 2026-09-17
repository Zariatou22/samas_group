<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlRequest;
use App\Http\Requests\Admin\UpdateBlRequest;
use App\Models\Bl;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerCompany;
use App\Models\DeliveryNote;
use App\Models\Exchange;
use App\Models\ProductType;
use App\Models\Transfert;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BlController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.bls.index', [
            'activeTab' => $request->query('activeTab', 'waiting'),
        ]);
    }

    /**
     * Alimente le DataTable de la page liste — reprend exactement la logique
     * de App\Filament\Resources\BlResource\Pages\ListBls::getTableQuery().
     */
    public function data(Request $request): JsonResponse
    {
        $bls = $this->scopedBls($request->query('activeTab', 'waiting'));

        $data = $bls->map(function (Bl $bl) {
            return [
                'id' => $bl->id,
                'bl' => $bl->bl,
                'customer_name' => $bl->mandataire?->customer_name,
                'customer_company_name' => $bl->customerCompany?->name,
                'containers_count' => $bl->containers_count,
                'container_labels' => $bl->containers->map(fn ($c) => trim($c->type_tc.' X '.$c->quantity))->all(),
                'eta_date' => optional($bl->containers->max('eta'))->format('Y-m-d'),
                'created' => optional($bl->created)->format('Y-m-d'),
                'company_name' => $bl->shippingCompany?->name,
                'description' => $bl->description,
                'type_operation' => $bl->type_operation,
                'exchange_date' => optional($bl->exchange?->date_received)->format('Y-m-d'),
                'bad_date' => optional($bl->deliveryNote?->date_received)->format('Y-m-d'),
                'valid_date' => optional($bl->deliveryNote?->date_valid)->format('Y-m-d'),
                'transfert_date' => optional($bl->transferts()->max('date_received'))->format('Y-m-d') ?: null,
                'observation' => $bl->observation,
                'is_started' => $bl->is_started,
                'is_completed' => $bl->is_completed,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Export Excel de l'onglet courant, comme
     * Bl::export_waiting/arrived/ongoing/completed/all() en CI.
     */
    public function export(Request $request): StreamedResponse
    {
        $activeTab = $request->query('activeTab', 'waiting');
        $bls = $this->scopedBls($activeTab);

        $labels = [
            'waiting' => 'BL en attente',
            'arrived' => 'BL arrivés',
            'ongoing' => 'BL en cours d\'opération',
            'completed' => 'BL clôturés',
            'all' => 'Tous les BL',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_strtoupper($labels[$activeTab] ?? 'BL'));

        $headers = ['N° B/L', 'Mandataire', 'Client', 'Compagnie', 'Type', 'Conteneurs', 'ETA', 'Date réception doc', 'Description marchandise', 'Echange BL', 'Réception BAD', 'Validité BAD', 'Date de transfert', 'Observations'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        $row = 2;
        foreach ($bls as $bl) {
            $containers = $bl->containers->map(fn ($c) => trim($c->type_tc.' X '.$c->quantity))->implode(', ');

            $sheet->fromArray([
                $bl->bl,
                $bl->mandataire?->customer_name,
                $bl->customerCompany?->name,
                $bl->shippingCompany?->name,
                $bl->type_operation,
                $containers ?: '-',
                optional($bl->containers->max('eta'))->format('d/m/Y') ?: '-',
                optional($bl->created)->format('d/m/Y') ?: '-',
                $bl->description,
                optional($bl->exchange?->date_received)->format('d/m/Y') ?: '-',
                optional($bl->deliveryNote?->date_received)->format('d/m/Y') ?: '-',
                optional($bl->deliveryNote?->date_valid)->format('d/m/Y') ?: '-',
                optional($bl->transferts()->max('date_received'))->format('d/m/Y') ?: '-',
                $bl->observation,
            ], null, "A{$row}");
            $row++;
        }

        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'bl-'.$activeTab.'-'.now()->format('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Bl>
     */
    private function scopedBls(string $activeTab)
    {
        $query = match ($activeTab) {
            'all' => Bl::query(),
            'arrived' => Bl::arrived(),
            'ongoing' => Bl::ongoing(),
            'completed' => Bl::completed(),
            default => Bl::waiting(),
        };

        return $query->with(['mandataire', 'customerCompany', 'shippingCompany', 'containers', 'exchange', 'deliveryNote'])
            ->withCount('containers')
            ->orderByDesc('created')
            ->get();
    }

    public function create(): View
    {
        return view('admin.bls.create', $this->formData());
    }

    public function store(StoreBlRequest $request): RedirectResponse
    {
        $bl = Bl::create($request->validated() + ['user' => auth()->id()]);

        return redirect()->route('admin.bls.edit', $bl)->with('success', 'BL enregistré avec succès.');
    }

    public function edit(Bl $bl): View
    {
        $containers = $bl->containers()->orderBy('numero')->get();

        return view('admin.bls.edit', $this->formData() + [
            'bl' => $bl,
            'containers' => $containers,
            'containerSizeTotals' => $this->containerSizeTotals($containers),
            'transferts' => $bl->transferts()->with('parentContainer')->orderByDesc('date_received')->get(),
        ]);
    }

    /**
     * Regroupe les conteneurs par taille (20/40/45 pieds), déduite du préfixe
     * numérique de `type_tc` (ex: "40HC", "20 DRY") faute de colonne dédiée.
     */
    private function containerSizeTotals(\Illuminate\Support\Collection $containers): \Illuminate\Support\Collection
    {
        return $containers
            ->groupBy(function ($container) {
                preg_match('/^\s*(\d{2})/', (string) $container->type_tc, $matches);

                return isset($matches[1]) ? $matches[1].' pieds' : 'Autre';
            })
            ->map->count();
    }

    public function update(UpdateBlRequest $request, Bl $bl): RedirectResponse
    {
        $bl->update($request->validated());

        return redirect()->route('admin.bls.index')->with('success', 'BL mis à jour avec succès.');
    }

    public function destroy(Bl $bl): RedirectResponse
    {
        $bl->delete();

        return back()->with('success', 'BL archivé.');
    }

    public function start(Bl $bl): RedirectResponse
    {
        $missing = $bl->missingStartRequirements();

        if (! empty($missing)) {
            return back()->with('error', "Impossible de démarrer l'opération : ".implode(', ', $missing).' manquant(e)(s).');
        }

        $bl->update(['is_started' => true]);

        return back()->with('success', 'Opération démarrée.');
    }

    public function complete(Bl $bl): RedirectResponse
    {
        $bl->update(['is_completed' => true]);

        return back()->with('success', 'BL clôturé.');
    }

    public function reopen(Bl $bl): RedirectResponse
    {
        $bl->update(['is_completed' => false]);

        return back()->with('success', 'BL rouvert.');
    }

    /**
     * Fait repasser un BL "en cours" à "arrivé" (avant démarrage) : annule le
     * démarrage et supprime l'échange BL / BAD enregistrés, comme
     * Bls::restore_bl_waiting()/delete_bl_exchange()/delete_bl_bad() en CI.
     */
    public function unstart(Bl $bl): RedirectResponse
    {
        $bl->update(['is_started' => false]);
        $bl->exchange?->delete();
        $bl->deliveryNote?->delete();

        return back()->with('success', 'BL remis en attente d\'échange BL / BAD.');
    }

    public function exchange(Request $request, Bl $bl): RedirectResponse
    {
        $data = $request->validate(['date_received' => ['nullable', 'date']]);

        Exchange::updateOrCreate(['bl' => $bl->id], ['user' => auth()->id(), 'date_received' => $data['date_received']]);

        return back()->with('success', 'Échange BL enregistré.');
    }

    public function bad(Request $request, Bl $bl): RedirectResponse
    {
        $data = $request->validate([
            'date_received' => ['nullable', 'date'],
            'date_valid' => ['nullable', 'date'],
        ]);

        DeliveryNote::updateOrCreate(['bl' => $bl->id], ['user' => auth()->id(), ...$data]);

        return back()->with('success', 'BAD enregistré.');
    }

    public function observation(Request $request, Bl $bl): RedirectResponse
    {
        $data = $request->validate(['observation' => ['nullable', 'string']]);

        $bl->update($data);

        return back()->with('success', 'Observation enregistrée.');
    }

    public function transfert(Request $request, Bl $bl): RedirectResponse
    {
        $data = $request->validate([
            'containers' => ['required', 'array', 'min:1'],
            'containers.*' => ['exists:containers,id'],
            'date_received' => ['required', 'date'],
        ]);

        foreach ($data['containers'] as $containerId) {
            Transfert::updateOrCreate(
                ['bl' => $bl->id, 'container' => $containerId],
                ['user' => auth()->id(), 'date_received' => $data['date_received']],
            );
        }

        return back()->with('success', count($data['containers']).' conteneur(s) transféré(s).');
    }

    public function transfertDestroy(Bl $bl, Transfert $transfert): RedirectResponse
    {
        $transfert->delete();

        return back()->with('success', 'Transfert annulé.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function formData(): array
    {
        return [
            'customers' => Customer::query()->orderBy('customer_name')->get(),
            'companies' => Company::query()->orderBy('name')->get(),
            'productTypes' => ProductType::query()->orderBy('name')->get(),
            'customerCompanies' => CustomerCompany::query()->orderBy('name')->get(['id', 'name', 'customer']),
        ];
    }
}
