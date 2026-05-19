<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dispatch\LoadController;
use App\Http\Controllers\Dispatch\AssignmentController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Dispatch
    Route::get('/dispatch', [LoadController::class, 'index'])
        ->name('dispatch.index');

    Route::post('/dispatch/loads/{load}/contact-driver', [AssignmentController::class, 'contactDriver'])
        ->name('dispatch.contact-driver');

    Route::post('/dispatch/loads/{load}/report-issue', [AssignmentController::class, 'reportIssue'])
        ->name('dispatch.report-issue');

    // Stubs — replace as each section is built
    Route::get('/orders', fn() => 'Orders coming soon')
        ->name('orders.index');

    Route::get('/fleet/drivers', fn() => 'Drivers coming soon')
        ->name('fleet.drivers.index');

    Route::get('/fleet/vehicles', fn() => 'Vehicles coming soon')
        ->name('fleet.vehicles.index');

    Route::get('/customers', fn() => 'Customers coming soon')
        ->name('customers.index');

    Route::get('/finance/invoices', fn() => 'Finance coming soon')
        ->name('finance.invoices.index');

});
