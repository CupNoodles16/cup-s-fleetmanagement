<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Stub routes — replace with real controllers as we build each section
    Route::get('/dispatch', fn() => 'Dispatch board coming soon')->name('dispatch.index');
    Route::get('/orders', fn() => 'Orders coming soon')->name('orders.index');
    Route::get('/fleet/drivers', fn() => 'Drivers coming soon')->name('fleet.drivers.index');
    Route::get('/fleet/vehicles', fn() => 'Vehicles coming soon')->name('fleet.vehicles.index');
    Route::get('/customers', fn() => 'Customers coming soon')->name('customers.index');
    Route::get('/finance/invoices', fn() => 'Finance coming soon')->name('finance.invoices.index');
});
