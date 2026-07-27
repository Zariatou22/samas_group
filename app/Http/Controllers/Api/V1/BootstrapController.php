<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Bl;
use App\Models\Car;
use App\Models\CarDriver;
use App\Models\CarOwner;
use App\Models\Company;
use App\Models\Container;
use App\Models\Customer;
use App\Models\CustomerCompany;
use App\Models\Loading;
use App\Models\LoadingContainer;
use App\Models\Source;
use Illuminate\Http\Request;

/**
 * Synchronisation initiale pour l'application mobile — équivalent de
 * Api_user::get_initialise_mobile() dans l'ancienne appli.
 */
class BootstrapController extends Controller
{
    public function initialiseMobile(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => [
                'bls' => Bl::all(),
                'containers' => Container::all(),
                'companies' => Company::all(),
                'customers' => Customer::all(),
                'customer_companies' => CustomerCompany::all(),
                'authorization' => Authorization::all(),
                'cars' => Car::all(),
                'owners' => CarOwner::all(),
                'drivers' => CarDriver::all(),
                'loadings' => Loading::all(),
                'loading_containers' => LoadingContainer::all(),
                'sources' => Source::all(),
            ],
        ]);
    }
}
