<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bl;
use App\Models\BlUnpot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Dépotage des conteneurs d'un BL de type DEPOTAGE, équivalent de
 * Bl::unpot()/unpot_add()/unpot_delete() en CI.
 */
class BlUnpotController extends Controller
{
    public function index(Bl $bl)
    {
        abort_unless($bl->type_operation === 'DEPOTAGE', 422, 'Le BL doit être de type DÉPOTAGE pour être dépoté.');

        return view('admin.bls.unpot', [
            'bl' => $bl,
            'containers' => $bl->containers()->orderBy('numero')->get(),
            'unpots' => $bl->unpots()->with('parentContainer')->orderByDesc('date_unpot')->get(),
        ]);
    }

    public function store(Request $request, Bl $bl): RedirectResponse
    {
        abort_unless($bl->type_operation === 'DEPOTAGE', 422, 'Le BL doit être de type DÉPOTAGE pour être dépoté.');

        $data = $request->validate([
            'container' => [
                'required',
                'exists:containers,id',
                Rule::unique('bl_unpot', 'container')->where('bl', $bl->id)->where('status', 0),
            ],
            'date_unpot' => ['required', 'date'],
        ], [
            'container.unique' => 'Ce conteneur est déjà marqué comme dépoté.',
        ]);

        BlUnpot::create($data + [
            'user' => auth()->id(),
            'bl' => $bl->id,
            'customer' => $bl->customer,
        ]);

        return back()->with('success', 'Conteneur dépoté.');
    }

    public function destroy(Bl $bl, BlUnpot $unpot): RedirectResponse
    {
        $unpot->delete();

        return back()->with('success', 'Dépotage annulé.');
    }
}
