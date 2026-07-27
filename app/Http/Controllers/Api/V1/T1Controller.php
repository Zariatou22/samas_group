<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Loading;
use App\Models\LoadingT1;
use Illuminate\Http\Request;

class T1Controller extends Controller
{
    public function waiting(Request $request)
    {
        $editId = $request->integer('edit_id');

        $loadings = Loading::query()
            ->where(function ($query) use ($editId) {
                $query->doesntHave('t1');
                if ($editId) {
                    $query->orWhereHas('t1', fn ($q) => $q->where('id', $editId));
                }
            })
            ->with(['parentBl', 'mandataire', 'vehicle'])
            ->get();

        return response()->json(['items' => $loadings]);
    }

    public function getOngoing()
    {
        return response()->json(['data' => LoadingT1::ongoing()->with(['parentLoading.parentBl'])->get()]);
    }

    public function getExpired()
    {
        return response()->json(['data' => LoadingT1::expired()->with(['parentLoading.parentBl'])->get()]);
    }

    public function getWaiting()
    {
        return response()->json(['data' => Loading::query()->doesntHave('t1')->with(['parentBl', 'mandataire'])->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $t1 = LoadingT1::create([...$data, 'user' => $request->user()->id]);

        return response()->json(['success' => true, 'message' => $t1->load('parentLoading')]);
    }

    public function update(Request $request, LoadingT1 $t1)
    {
        $data = $this->validated($request);

        $t1->update($data);

        return response()->json(['success' => true, 'message' => $t1->fresh('parentLoading')]);
    }

    public function validateT1(Request $request, LoadingT1 $t1)
    {
        $t1->update(['validate' => now()]);

        return response()->json(['success' => true, 'message' => $t1->fresh()]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'loading' => ['required', 'integer', 'exists:loading,id'],
            't1_number' => ['required', 'integer'],
            'valid_until' => ['required', 'date'],
        ]);
    }
}
