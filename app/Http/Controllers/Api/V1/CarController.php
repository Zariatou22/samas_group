<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarDriver;
use App\Models\CarOwner;

class CarController extends Controller
{
    public function getCars()
    {
        return response()->json(['data' => Car::with(['carOwner', 'carDriver'])->get()]);
    }

    public function getOwners()
    {
        return response()->json(['data' => CarOwner::all()]);
    }

    public function getDrivers()
    {
        return response()->json(['data' => CarDriver::with('carOwner')->get()]);
    }

    public function getDistinctCars()
    {
        return response()->json(['data' => Car::query()->pluck('full_registration')->unique()->values()]);
    }

    public function getDistinctOwners()
    {
        return response()->json(['data' => CarOwner::query()->pluck('name')->unique()->values()]);
    }

    public function getDistinctDrivers()
    {
        return response()->json(['data' => CarDriver::query()->pluck('name')->unique()->values()]);
    }
}
