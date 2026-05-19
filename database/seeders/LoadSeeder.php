<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Load;
use App\Models\Order;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;

class LoadSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();
        if ($orders->isEmpty()) {
            $this->command->info('No orders found. Skipping LoadSeeder.');
            return;
        }

        foreach ($orders as $idx => $order) {
            // Skip if a load already exists for this order
            if (Load::where('order_id', $order->id)->exists()) {
                continue;
            }

            // Statuses for the load (skip 'unassigned' for demo)
            $statuses = ['assigned', 'driver_accepted', 'en_route_pickup', 'at_pickup', 'loaded', 'en_route_delivery', 'at_delivery', 'delivered'];
            $status = $statuses[array_rand($statuses)];

            // Pick a random driver and vehicle
            $driver = Driver::inRandomOrder()->first();
            $vehicle = $driver ? ($driver->current_vehicle_id ? Vehicle::find($driver->current_vehicle_id) : Vehicle::inRandomOrder()->first()) : Vehicle::inRandomOrder()->first();

            // Optional delay
            $isDelayed = (rand(1,10) <= 2);
            $delayMinutes = $isDelayed ? rand(15, 90) : null;
            $etaAt = Carbon::now()->addHours(rand(1, 8));
            if ($isDelayed) {
                $etaAt->addMinutes($delayMinutes);
            }

            Load::create([
                'load_number' => 'LD-' . str_pad($idx+1, 5, '0', STR_PAD_LEFT),
                'order_id' => $order->id,
                'driver_id' => $driver?->id,
                'vehicle_id' => $vehicle?->id,
                'status' => $status,
                'is_delayed' => $isDelayed,
                'delay_minutes' => $delayMinutes,
                'eta_at' => $etaAt,
                'assigned_at' => $status !== 'unassigned' ? Carbon::now()->subHours(rand(1,12)) : null,
            ]);
        }

        $this->command->info('LoadSeeder completed. Created ' . Load::count() . ' loads.');
    }
}
