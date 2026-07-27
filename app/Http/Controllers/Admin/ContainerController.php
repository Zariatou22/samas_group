<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bl;
use App\Models\Container;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CRUD des conteneurs d'un BL, équivalent de
 * Bl::containers()/edit_container()/delete_container() en CI.
 */
class ContainerController extends Controller
{
    public function store(Request $request, Bl $bl): RedirectResponse
    {
        $data = $this->validateData($request, $bl);

        $bl->containers()->create($data + [
            'user' => auth()->id(),
            'customer' => $bl->customer,
            'customer_company' => $bl->customer_company,
        ]);

        return back()->with('success', 'Conteneur enregistré.');
    }

    public function update(Request $request, Bl $bl, Container $container): RedirectResponse
    {
        $data = $this->validateData($request, $bl, $container);

        $container->update($data);

        return back()->with('success', 'Conteneur mis à jour.');
    }

    public function destroy(Bl $bl, Container $container): RedirectResponse
    {
        $container->delete();

        return back()->with('success', 'Conteneur supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request, Bl $bl, ?Container $editing = null): array
    {
        return $request->validate([
            'type_tc' => ['required', 'string', 'max:50'],
            'numero' => [
                'required', 'string', 'max:50',
                Rule::unique('containers', 'numero')
                    ->where('bl', $bl->id)
                    ->where('type_tc', $request->input('type_tc'))
                    ->where('status', 0)
                    ->ignore($editing?->id),
            ],
            'ship' => ['required', 'string', 'max:255'],
            'eta' => ['required', 'date'],
            'lead_number' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', 'exists:product_type,id'],
            'nb_package' => ['nullable', 'numeric'],
            'quantity' => ['nullable', 'numeric'],
        ], [
            'numero.unique' => 'Un conteneur avec le même numéro est déjà enregistré sur ce BL.',
        ]);
    }
}
