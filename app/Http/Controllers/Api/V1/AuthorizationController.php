<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Bl;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthorizationController extends Controller
{
    public function getAuth(Request $request)
    {
        $completed = $request->boolean('completed');

        $query = Authorization::with(['parentBl', 'mandataire', 'customerCompany', 'pickupSource']);
        $query = $completed ? $query->settled() : $query->ongoing();

        return response()->json(['data' => $query->get()]);
    }

    public function getAvailableAuth(Request $request)
    {
        $blId = $request->integer('bl');

        if (! $blId) {
            return response()->json(['data' => []]);
        }

        $authorizations = Authorization::query()
            ->where('bl', $blId)
            ->ongoing()
            ->with(['pickupSource'])
            ->get();

        return response()->json(['data' => $authorizations]);
    }

    public function getAvailableAuthForLoading(Request $request)
    {
        $blId = $request->integer('bl');
        $editId = $request->integer('edit_id');
        $q = trim((string) $request->input('q', ''));

        if (! $blId) {
            return response()->json(['items' => []]);
        }

        $items = Authorization::query()
            ->where('bl', $blId)
            ->where(function ($query) use ($editId) {
                $query->ongoing();
                if ($editId) {
                    $query->orWhere('id', $editId);
                }
            })
            ->when($q !== '', fn ($query) => $query->where('auth_number', 'like', "%{$q}%"))
            ->with(['pickupSource'])
            ->get();

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $bl = Bl::findOrFail($data['bl']);

        $authorization = Authorization::create([
            ...$data,
            'user' => $request->user()->id,
            'customer' => $bl->customer,
            'customer_company' => $bl->customer_company ?? 0,
        ]);

        return response()->json(['success' => true, 'message' => $authorization->load(['parentBl', 'pickupSource'])]);
    }

    public function update(Request $request, Authorization $authorization)
    {
        $data = $this->validated($request, $authorization->id);

        $authorization->update($data);

        return response()->json(['success' => true, 'message' => $authorization->fresh(['parentBl', 'pickupSource'])]);
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'bl' => ['required', 'integer', 'exists:bl,id'],
            'auth_number' => ['required', 'string', 'max:45', Rule::unique('authorization', 'auth_number')->ignore($ignoreId)],
            'nb_container' => ['required', 'integer', 'min:0'],
            'nb_package' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'source' => ['required', 'integer', 'exists:sources,id'],
        ]);
    }
}
