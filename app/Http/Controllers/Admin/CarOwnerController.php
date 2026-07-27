<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarOwner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarOwnerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
        ]);

        $owner = CarOwner::create($data + ['user' => auth()->id()]);

        return back()->with('success', 'Transporteur ajouté.')->with('newOwnerId', $owner->id);
    }
}
