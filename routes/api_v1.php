<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorizationController;
use App\Http\Controllers\Api\V1\BlController;
use App\Http\Controllers\Api\V1\BootstrapController;
use App\Http\Controllers\Api\V1\CarController;
use App\Http\Controllers\Api\V1\ContainerController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LoadingController;
use App\Http\Controllers\Api\V1\T1Controller;
use Illuminate\Support\Facades\Route;

// Connexion — pas de middleware, c'est elle qui délivre le jeton.
Route::post('user/auth', [AuthController::class, 'auth']);

Route::middleware('mobile.auth')->group(function () {
    Route::post('user/update-password', [AuthController::class, 'updatePassword']);
    Route::post('user/update-user-info', [AuthController::class, 'updateUserInfo']);
    Route::post('user/get-initialise-mobile', [BootstrapController::class, 'initialiseMobile']);

    Route::get('bl/list-bls', [BlController::class, 'listBls']);
    Route::get('bl/containers', [BlController::class, 'containers']);
    Route::get('bl/operations', [BlController::class, 'operations']);
    Route::get('bl/get-available', [BlController::class, 'getAvailable']);
    Route::get('bl/get-waiting-week', [BlController::class, 'getWaitingWeek']);
    Route::post('bl/set-observation', [BlController::class, 'setObservation']);

    Route::get('car/get-cars', [CarController::class, 'getCars']);
    Route::get('car/get-owners', [CarController::class, 'getOwners']);
    Route::get('car/get-drivers', [CarController::class, 'getDrivers']);
    Route::get('car/get-distinct-cars', [CarController::class, 'getDistinctCars']);
    Route::get('car/get-distinct-owners', [CarController::class, 'getDistinctOwners']);
    Route::get('car/get-distinct-drivers', [CarController::class, 'getDistinctDrivers']);

    Route::get('container/get-containers', [ContainerController::class, 'getContainers']);
    Route::get('container/get-available-containers', [ContainerController::class, 'getAvailableContainers']);
    Route::get('container/get-available-containers-for-loading', [ContainerController::class, 'getAvailableContainersForLoading']);

    Route::get('authorization/get-auth', [AuthorizationController::class, 'getAuth']);
    Route::get('authorization/get-available-auth', [AuthorizationController::class, 'getAvailableAuth']);
    Route::get('authorization/get-available-auth-for-loading', [AuthorizationController::class, 'getAvailableAuthForLoading']);
    Route::post('authorization', [AuthorizationController::class, 'store']);
    Route::put('authorization/{authorization}', [AuthorizationController::class, 'update']);

    Route::get('loading/get-loadings', [LoadingController::class, 'getLoadings']);
    Route::post('loading', [LoadingController::class, 'store']);
    Route::put('loading/{loading}', [LoadingController::class, 'update']);

    Route::get('t1/waiting', [T1Controller::class, 'waiting']);
    Route::get('t1/get-ongoing', [T1Controller::class, 'getOngoing']);
    Route::get('t1/get-expired', [T1Controller::class, 'getExpired']);
    Route::get('t1/get-waiting', [T1Controller::class, 'getWaiting']);
    Route::post('t1', [T1Controller::class, 'store']);
    Route::put('t1/{t1}', [T1Controller::class, 'update']);
    Route::post('t1/{t1}/validate', [T1Controller::class, 'validateT1']);

    Route::get('customer/search', [CustomerController::class, 'search']);
    Route::get('customer/search-companies', [CustomerController::class, 'searchCompanies']);
    Route::get('customer/search-companies-with-commands', [CustomerController::class, 'searchCompaniesWithCommands']);
    Route::get('customer/users', [CustomerController::class, 'users']);
    Route::get('customer/companies', [CustomerController::class, 'companies']);
    Route::get('customer/get-customer-company', [CustomerController::class, 'getCustomerCompany']);

    Route::get('invoice/get-invoices', [InvoiceController::class, 'getInvoices']);
    Route::get('invoice/get-invoice-customers', [InvoiceController::class, 'getInvoiceCustomers']);
    Route::get('invoice/get-customer-invoices', [InvoiceController::class, 'getCustomerInvoices']);
    Route::get('invoice/get-bl-invoices', [InvoiceController::class, 'getBlInvoices']);
    Route::get('invoice/get-labels', [InvoiceController::class, 'getLabels']);
    Route::post('invoice/set-invoice-label', [InvoiceController::class, 'setInvoiceLabel']);
});
