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
        $query = match ($request->query('activeTab', 'waiting')) {
            'all' => Bl::query(),
            'arrived' => Bl::arrived(),
            'ongoing' => Bl::ongoing(),
            'completed' => Bl::completed(),
            default => Bl::waiting(),
        };

        $bls = $query->with(['mandataire', 'customerCompany', 'shippingCompany', 'containers', 'exchange', 'deliveryNote'])
            ->withCount('containers')
            ->orderByDesc('created')
            ->get();

        $data = $bls->map(function (Bl $bl) {
            return [
                'id' => $bl->id,
                'bl' => $bl->bl,
                'customer_name' => $bl->mandataire?->customer_name,
                'customer_company_name' => $bl->customerCompany?->name,
                'containers_count' => $bl->containers_count,
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
        return view('admin.bls.edit', $this->formData() + [
            'bl' => $bl,
            'containers' => $bl->containers()->orderBy('numero')->get(),
            'transferts' => $bl->transferts()->with('parentContainer')->orderByDesc('date_received')->get(),
        ]);
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
            'container' => ['required', 'exists:containers,id'],
            'date_received' => ['required', 'date'],
        ]);

        Transfert::updateOrCreate(
            ['bl' => $bl->id, 'container' => $data['container']],
            ['user' => auth()->id(), 'date_received' => $data['date_received']],
        );

        return back()->with('success', 'Transfert enregistré.');
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
