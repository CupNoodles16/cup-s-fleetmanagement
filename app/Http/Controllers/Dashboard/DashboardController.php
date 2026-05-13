<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\Order;
use App\Models\Driver;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index', [
            'activeLoads'     => Load::whereNotIn('status', ['delivered','cancelled','failed'])->count(),
            'unassignedOrders'=> Order::where('status','confirmed')->count(),
            'exceptions'      => Load::where('is_delayed', true)->orWhere('status','failed')->count(),
            'driversOnDuty'   => Driver::whereIn('status',['available','on_trip'])->count(),
            'totalDrivers'    => Driver::where('status','!=','suspended')->count(),
            'recentLoads'     => Load::with(['driver.user','vehicle','order.stops'])
                                     ->whereNotIn('status',['delivered','cancelled'])
                                     ->latest()->limit(8)->get(),
            'pendingOrders'   => Order::where('status','confirmed')
                                      ->orderByRaw("CASE priority WHEN 'critical' THEN 0 WHEN 'urgent' THEN 1 ELSE 2 END")
                                      ->limit(5)->get(),
        ]);
    }
}
