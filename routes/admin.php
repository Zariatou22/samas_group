<?php

use App\Http\Controllers\Admin\AccountingInvoiceController;
use App\Http\Controllers\Admin\AccountingInvoiceFieldRegularController;
use App\Http\Controllers\Admin\AccountingInvoiceLabelController;
use App\Http\Controllers\Admin\AuthorizationController;
use App\Http\Controllers\Admin\BlController;
use App\Http\Controllers\Admin\BlUnpotController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\CarDriverController;
use App\Http\Controllers\Admin\CarOwnerController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ContainerController;
use App\Http\Controllers\Admin\ContainerTrackingController;
use App\Http\Controllers\Admin\CustomerCompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerDocumentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\InvoiceAdvanceController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceLabelController;
use App\Http\Controllers\Admin\LoadingController;
use App\Http\Controllers\Admin\LoadingT1Controller;
use App\Http\Controllers\Admin\MandataireBalanceController;
use App\Http\Controllers\Admin\PermController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\UserActionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/containers-per-month', [DashboardController::class, 'containersPerMonth'])->name('dashboard.containers-per-month');
    Route::get('/dashboard/week-arrivals', [DashboardController::class, 'weekArrivals'])->name('dashboard.week-arrivals');

    Route::middleware('can.module')->group(function () {
        Route::get('/container-tracking', [ContainerTrackingController::class, 'index'])->name('container-tracking.index');
        Route::get('/container-tracking/data', [ContainerTrackingController::class, 'data'])->name('container-tracking.data');

        Route::get('/bls', [BlController::class, 'index'])->name('bls.index');
        Route::get('/bls/data', [BlController::class, 'data'])->name('bls.data');
        Route::get('/bls/export', [BlController::class, 'export'])->name('bls.export');
        Route::get('/bls/create', [BlController::class, 'create'])->name('bls.create');
        Route::post('/bls', [BlController::class, 'store'])->name('bls.store');
        Route::get('/bls/{bl}/edit', [BlController::class, 'edit'])->name('bls.edit');
        Route::put('/bls/{bl}', [BlController::class, 'update'])->name('bls.update');
        Route::delete('/bls/{bl}', [BlController::class, 'destroy'])->name('bls.destroy');

        Route::post('/bls/{bl}/start', [BlController::class, 'start'])->name('bls.start');
        Route::post('/bls/{bl}/complete', [BlController::class, 'complete'])->name('bls.complete');
        Route::post('/bls/{bl}/reopen', [BlController::class, 'reopen'])->name('bls.reopen');
        Route::post('/bls/{bl}/unstart', [BlController::class, 'unstart'])->name('bls.unstart');
        Route::post('/bls/{bl}/exchange', [BlController::class, 'exchange'])->name('bls.exchange');
        Route::post('/bls/{bl}/bad', [BlController::class, 'bad'])->name('bls.bad');
        Route::post('/bls/{bl}/observation', [BlController::class, 'observation'])->name('bls.observation');
        Route::post('/bls/{bl}/transfert', [BlController::class, 'transfert'])->name('bls.transfert');
        Route::delete('/bls/{bl}/transfert/{transfert}', [BlController::class, 'transfertDestroy'])->name('bls.transfert.destroy');

        Route::post('/bls/{bl}/containers', [ContainerController::class, 'store'])->name('bls.containers.store');
        Route::put('/bls/{bl}/containers/{container}', [ContainerController::class, 'update'])->name('bls.containers.update');
        Route::delete('/bls/{bl}/containers/{container}', [ContainerController::class, 'destroy'])->name('bls.containers.destroy');

        Route::get('/bls/{bl}/unpot', [BlUnpotController::class, 'index'])->name('bls.unpot.index');
        Route::post('/bls/{bl}/unpot', [BlUnpotController::class, 'store'])->name('bls.unpot.store');
        Route::delete('/bls/{bl}/unpot/{unpot}', [BlUnpotController::class, 'destroy'])->name('bls.unpot.destroy');

        Route::get('/product-types', [ProductTypeController::class, 'index'])->name('product-types.index');
        Route::get('/product-types/data', [ProductTypeController::class, 'data'])->name('product-types.data');
        Route::post('/product-types', [ProductTypeController::class, 'store'])->name('product-types.store');
        Route::put('/product-types/{productType}', [ProductTypeController::class, 'update'])->name('product-types.update');
        Route::delete('/product-types/{productType}', [ProductTypeController::class, 'destroy'])->name('product-types.destroy');

        Route::get('/sources', [SourceController::class, 'index'])->name('sources.index');
        Route::get('/sources/data', [SourceController::class, 'data'])->name('sources.data');
        Route::post('/sources', [SourceController::class, 'store'])->name('sources.store');
        Route::put('/sources/{source}', [SourceController::class, 'update'])->name('sources.update');
        Route::delete('/sources/{source}', [SourceController::class, 'destroy'])->name('sources.destroy');

        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/data', [CompanyController::class, 'data'])->name('companies.data');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

        Route::get('/authorizations', [AuthorizationController::class, 'index'])->name('authorizations.index');
        Route::get('/authorizations/data', [AuthorizationController::class, 'data'])->name('authorizations.data');
        Route::get('/authorizations/create', [AuthorizationController::class, 'create'])->name('authorizations.create');
        Route::post('/authorizations', [AuthorizationController::class, 'store'])->name('authorizations.store');
        Route::get('/authorizations/{authorization}/edit', [AuthorizationController::class, 'edit'])->name('authorizations.edit');
        Route::put('/authorizations/{authorization}', [AuthorizationController::class, 'update'])->name('authorizations.update');
        Route::delete('/authorizations/{authorization}', [AuthorizationController::class, 'destroy'])->name('authorizations.destroy');
    });

    Route::middleware('can.module')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/data', [CustomerController::class, 'data'])->name('customers.data');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        Route::get('/customers/{customer}/companies/{company}', [CustomerCompanyController::class, 'show'])->name('customers.companies.show');
        Route::post('/customers/{customer}/companies', [CustomerCompanyController::class, 'store'])->name('customers.companies.store');
        Route::put('/customers/{customer}/companies/{company}', [CustomerCompanyController::class, 'update'])->name('customers.companies.update');
        Route::delete('/customers/{customer}/companies/{company}', [CustomerCompanyController::class, 'destroy'])->name('customers.companies.destroy');

        Route::get('/customers/{customer}/transfer', [CustomerController::class, 'transferForm'])->name('customers.transfer.form');
        Route::post('/customers/{customer}/transfer', [CustomerController::class, 'transfer'])->name('customers.transfer');
        Route::post('/customers/{customer}/companies/{company}/transfer', [CustomerController::class, 'transferCompany'])->name('customers.companies.transfer');

        Route::get('/customers/{customer}/documents', [CustomerDocumentController::class, 'index'])->name('customers.documents.index');
        Route::post('/customers/{customer}/documents', [CustomerDocumentController::class, 'store'])->name('customers.documents.store');
        Route::put('/customers/{customer}/documents/{document}', [CustomerDocumentController::class, 'update'])->name('customers.documents.update');
        Route::delete('/customers/{customer}/documents/{document}', [CustomerDocumentController::class, 'destroy'])->name('customers.documents.destroy');
    });

    Route::middleware('can.module')->group(function () {
        Route::get('/loadings', [LoadingController::class, 'index'])->name('loadings.index');
        Route::get('/loadings/data', [LoadingController::class, 'data'])->name('loadings.data');
        Route::get('/loadings/create', [LoadingController::class, 'create'])->name('loadings.create');
        Route::post('/loadings', [LoadingController::class, 'store'])->name('loadings.store');
        Route::get('/loadings/{loading}/edit', [LoadingController::class, 'edit'])->name('loadings.edit');
        Route::put('/loadings/{loading}', [LoadingController::class, 'update'])->name('loadings.update');
        Route::delete('/loadings/{loading}', [LoadingController::class, 'destroy'])->name('loadings.destroy');
    });

    Route::middleware('can.module')->group(function () {
        Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
        Route::get('/cars/data', [CarController::class, 'data'])->name('cars.data');
        Route::get('/cars/create', [CarController::class, 'create'])->name('cars.create');
        Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
        Route::get('/cars/{car}/edit', [CarController::class, 'edit'])->name('cars.edit');
        Route::put('/cars/{car}', [CarController::class, 'update'])->name('cars.update');
        Route::delete('/cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy');

        Route::get('/car-owners', [CarOwnerController::class, 'index'])->name('car-owners.index');
        Route::get('/car-owners/data', [CarOwnerController::class, 'data'])->name('car-owners.data');
        Route::post('/car-owners', [CarOwnerController::class, 'store'])->name('car-owners.store');
        Route::put('/car-owners/{carOwner}', [CarOwnerController::class, 'update'])->name('car-owners.update');
        Route::delete('/car-owners/{carOwner}', [CarOwnerController::class, 'destroy'])->name('car-owners.destroy');

        Route::get('/car-drivers', [CarDriverController::class, 'index'])->name('car-drivers.index');
        Route::get('/car-drivers/data', [CarDriverController::class, 'data'])->name('car-drivers.data');
        Route::post('/car-drivers', [CarDriverController::class, 'store'])->name('car-drivers.store');
        Route::put('/car-drivers/{carDriver}', [CarDriverController::class, 'update'])->name('car-drivers.update');
        Route::delete('/car-drivers/{carDriver}', [CarDriverController::class, 'destroy'])->name('car-drivers.destroy');
    });

    Route::middleware('can.module')->group(function () {
        Route::get('/loading-t1s', [LoadingT1Controller::class, 'index'])->name('loading-t1s.index');
        Route::get('/loading-t1s/data', [LoadingT1Controller::class, 'data'])->name('loading-t1s.data');
        Route::get('/loading-t1s/create', [LoadingT1Controller::class, 'create'])->name('loading-t1s.create');
        Route::post('/loading-t1s', [LoadingT1Controller::class, 'store'])->name('loading-t1s.store');
        Route::get('/loading-t1s/{loadingT1}/edit', [LoadingT1Controller::class, 'edit'])->name('loading-t1s.edit');
        Route::put('/loading-t1s/{loadingT1}', [LoadingT1Controller::class, 'update'])->name('loading-t1s.update');
        Route::post('/loading-t1s/validate-bulk', [LoadingT1Controller::class, 'validateBulk'])->name('loading-t1s.validate-bulk');
        Route::post('/loading-t1s/{loadingT1}/validate', [LoadingT1Controller::class, 'validateT1'])->name('loading-t1s.validate');
        Route::delete('/loading-t1s/{loadingT1}', [LoadingT1Controller::class, 'destroy'])->name('loading-t1s.destroy');
    });

    Route::middleware('can.module')->group(function () {
        Route::get('/mandataire-balances', [MandataireBalanceController::class, 'index'])->name('mandataire-balances.index');
        Route::get('/mandataire-balances/data', [MandataireBalanceController::class, 'data'])->name('mandataire-balances.data');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/data', [InvoiceController::class, 'data'])->name('invoices.data');
        Route::get('/invoices/bl/{bl}', [InvoiceController::class, 'blInvoices'])->name('invoices.bl');
        Route::get('/invoices/bl/{bl}/data', [InvoiceController::class, 'blData'])->name('invoices.bl.data');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::get('/invoice-labels', [InvoiceLabelController::class, 'index'])->name('invoice-labels.index');
        Route::get('/invoice-labels/data', [InvoiceLabelController::class, 'data'])->name('invoice-labels.data');
        Route::post('/invoice-labels', [InvoiceLabelController::class, 'store'])->name('invoice-labels.store');
        Route::put('/invoice-labels/{invoiceLabel}', [InvoiceLabelController::class, 'update'])->name('invoice-labels.update');
        Route::delete('/invoice-labels/{invoiceLabel}', [InvoiceLabelController::class, 'destroy'])->name('invoice-labels.destroy');

        Route::get('/invoice-advances', [InvoiceAdvanceController::class, 'index'])->name('invoice-advances.index');
        Route::get('/invoice-advances/data', [InvoiceAdvanceController::class, 'data'])->name('invoice-advances.data');
        Route::get('/invoice-advances/create', [InvoiceAdvanceController::class, 'create'])->name('invoice-advances.create');
        Route::post('/invoice-advances', [InvoiceAdvanceController::class, 'store'])->name('invoice-advances.store');
        Route::get('/invoice-advances/{invoiceAdvance}/edit', [InvoiceAdvanceController::class, 'edit'])->name('invoice-advances.edit');
        Route::put('/invoice-advances/{invoiceAdvance}', [InvoiceAdvanceController::class, 'update'])->name('invoice-advances.update');
        Route::delete('/invoice-advances/{invoiceAdvance}', [InvoiceAdvanceController::class, 'destroy'])->name('invoice-advances.destroy');
        Route::get('/invoice-advances/{invoiceAdvance}/print', [InvoiceAdvanceController::class, 'print'])->name('invoice-advances.print');
        Route::get('/invoice-advances/driver-info/{carDriver}', [InvoiceAdvanceController::class, 'driverInfo'])->name('invoice-advances.driver-info');

        Route::get('/accounting-invoices', [AccountingInvoiceController::class, 'index'])->name('accounting-invoices.index');
        Route::get('/accounting-invoices/data', [AccountingInvoiceController::class, 'data'])->name('accounting-invoices.data');
        Route::get('/accounting-invoices/create', [AccountingInvoiceController::class, 'create'])->name('accounting-invoices.create');
        Route::post('/accounting-invoices', [AccountingInvoiceController::class, 'store'])->name('accounting-invoices.store');
        Route::get('/accounting-invoices/{accountingInvoice}/edit', [AccountingInvoiceController::class, 'edit'])->name('accounting-invoices.edit');
        Route::put('/accounting-invoices/{accountingInvoice}', [AccountingInvoiceController::class, 'update'])->name('accounting-invoices.update');
        Route::delete('/accounting-invoices/{accountingInvoice}', [AccountingInvoiceController::class, 'destroy'])->name('accounting-invoices.destroy');

        Route::get('/accounting-invoice-labels', [AccountingInvoiceLabelController::class, 'index'])->name('accounting-invoice-labels.index');
        Route::get('/accounting-invoice-labels/data', [AccountingInvoiceLabelController::class, 'data'])->name('accounting-invoice-labels.data');
        Route::post('/accounting-invoice-labels', [AccountingInvoiceLabelController::class, 'store'])->name('accounting-invoice-labels.store');
        Route::put('/accounting-invoice-labels/{accountingInvoiceLabel}', [AccountingInvoiceLabelController::class, 'update'])->name('accounting-invoice-labels.update');
        Route::delete('/accounting-invoice-labels/{accountingInvoiceLabel}', [AccountingInvoiceLabelController::class, 'destroy'])->name('accounting-invoice-labels.destroy');

        Route::get('/accounting-invoice-field-regulars', [AccountingInvoiceFieldRegularController::class, 'index'])->name('accounting-invoice-field-regulars.index');
        Route::get('/accounting-invoice-field-regulars/data', [AccountingInvoiceFieldRegularController::class, 'data'])->name('accounting-invoice-field-regulars.data');
        Route::post('/accounting-invoice-field-regulars', [AccountingInvoiceFieldRegularController::class, 'store'])->name('accounting-invoice-field-regulars.store');
        Route::put('/accounting-invoice-field-regulars/{accountingInvoiceFieldRegular}', [AccountingInvoiceFieldRegularController::class, 'update'])->name('accounting-invoice-field-regulars.update');
        Route::delete('/accounting-invoice-field-regulars/{accountingInvoiceFieldRegular}', [AccountingInvoiceFieldRegularController::class, 'destroy'])->name('accounting-invoice-field-regulars.destroy');
    });

    Route::middleware('has.level:Edition')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/user-actions', [UserActionController::class, 'index'])->name('user-actions.index');
        Route::get('/user-actions/data', [UserActionController::class, 'data'])->name('user-actions.data');

        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::get('/groups/data', [GroupController::class, 'data'])->name('groups.data');
        Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
        Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
        Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
        Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

        Route::get('/perms', [PermController::class, 'index'])->name('perms.index');
        Route::get('/perms/data', [PermController::class, 'data'])->name('perms.data');
        Route::post('/perms', [PermController::class, 'store'])->name('perms.store');
        Route::put('/perms/{perm}', [PermController::class, 'update'])->name('perms.update');
        Route::delete('/perms/{perm}', [PermController::class, 'destroy'])->name('perms.destroy');
    });

    Route::middleware('has.level:Administration')->group(function () {
        Route::get('/general-settings', [SettingsController::class, 'general'])->name('settings.general');
        Route::post('/general-settings', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');

        Route::get('/email-settings', [SettingsController::class, 'email'])->name('settings.email');
        Route::post('/email-settings', [SettingsController::class, 'updateEmail'])->name('settings.email.update');
    });
});
