<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Order;
use App\Models\OrderStop;
use App\Models\Load;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DispatchDemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1. Add additional customers if fewer than 50 exist
        $existingCustomerCount = Customer::count();
        if ($existingCustomerCount < 50) {
            $needed = 50 - $existingCustomerCount;
            for ($i = 1; $i <= $needed; $i++) {
                Customer::firstOrCreate(
                    ['email' => $faker->unique()->safeEmail],
                    [
                        'company_name' => $faker->company,
                        'contact_person' => $faker->name,
                        'phone' => '0917' . str_pad($existingCustomerCount + $i, 7, '0', STR_PAD_LEFT),
                        'billing_address' => $faker->streetAddress,
                        'billing_city' => $faker->city,
                        'billing_province' => 'Cebu',
                        'billing_zip' => $faker->postcode,
                        'status' => 'active',
                    ]
                );
            }
        }

        // 2. Dispatcher user
        $dispatcher = User::firstOrCreate(
            ['email' => 'dispatch@cupdispatch.com'],
            [
                'name' => 'Dispatch Admin',
                'password' => Hash::make('password'),
                'role' => 'dispatcher',
                'is_active' => true,
            ]
        );

        // 3. Create up to 10 driver users and driver records
        for ($i = 1; $i <= 10; $i++) {
            $user = User::firstOrCreate(
                ['email' => "driver$i@cupdispatch.com"],
                [
                    'name' => $faker->name,
                    'password' => Hash::make('password'),
                    'role' => 'driver',
                    'is_active' => true,
                ]
            );
            if (!Driver::where('user_id', $user->id)->exists()) {
                $vehicle = Vehicle::inRandomOrder()->first() ?? Vehicle::firstOrCreate([
                    'plate_number' => 'C' . strtoupper($faker->bothify('??-####')),
                    'model' => $faker->company . ' ' . $faker->word,
                    'type' => 'closed_van',
                    'capacity_kg' => rand(1000, 15000),
                    'status' => 'available',
                ]);
                Driver::create([
                    'user_id' => $user->id,
                    'current_vehicle_id' => $vehicle->id,
                    'license_number' => 'L' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'license_type' => 'professional',
                    'license_expiry' => Carbon::now()->addYears(3),
                    'status' => $faker->randomElement(['available','on_trip','off_duty']),
                    'hos_remaining_minutes' => rand(200, 600),
                    'performance_rating' => rand(30, 50) / 10,
                    'phone' => '0918' . str_pad($i, 7, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // 4. Add vehicles if fewer than 15
        if (Vehicle::count() < 15) {
            $vehicleTypes = ['closed_van','open_truck','flatbed','refrigerated','tanker','trailer'];
            $needed = 15 - Vehicle::count();
            for ($i = 1; $i <= $needed; $i++) {
                Vehicle::firstOrCreate(
                    ['plate_number' => 'C' . strtoupper($faker->bothify('??-####'))],
                    [
                        'model' => $faker->company . ' ' . $faker->word,
                        'type' => $vehicleTypes[array_rand($vehicleTypes)],
                        'capacity_kg' => rand(1000, 15000),
                        'status' => $faker->randomElement(['available','on_trip','in_maintenance']),
                    ]
                );
            }
        }

        // 5. Add orders if fewer than 50
        $existingOrders = Order::count();
        if ($existingOrders < 50) {
            $cities = ['Cebu City', 'Mandaue City', 'Lapu-Lapu City', 'Talisay City', 'Consolacion', 'Liloan', 'Naga', 'Danao'];
            $statuses = ['draft', 'confirmed', 'assigned', 'in_transit', 'delivered'];
            $priorities = ['normal', 'urgent', 'critical'];
            $cargoTypes = ['general', 'fragile', 'perishable', 'hazardous', 'bulk'];

            $needed = 50 - $existingOrders;
            for ($i = 1; $i <= $needed; $i++) {
                $customer = Customer::inRandomOrder()->first();
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];
                $cargoType = $cargoTypes[array_rand($cargoTypes)];

                $order = Order::create([
                    'order_number' => 'ORD-DEMO-' . str_pad($existingOrders + $i, 5, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'created_by' => $dispatcher->id,
                    'cargo_description' => $faker->sentence(3),
                    'cargo_type' => $cargoType,
                    'weight_kg' => rand(500, 10000),
                    'required_vehicle_type' => 'any',
                    'status' => $status,
                    'priority' => $priority,
                    'delivery_deadline_at' => Carbon::now()->addHours(rand(2, 48)),
                    'confirmed_at' => in_array($status, ['confirmed','assigned','in_transit','delivered']) ? Carbon::now()->subHours(rand(1,24)) : null,
                ]);

                // Stops
                $pickupCity = $cities[array_rand($cities)];
                $deliveryCity = $cities[array_rand($cities)];
                while ($deliveryCity === $pickupCity) {
                    $deliveryCity = $cities[array_rand($cities)];
                }
                OrderStop::create([
                    'order_id' => $order->id,
                    'sequence' => 1,
                    'type' => 'pickup',
                    'city' => $pickupCity,
                    'address_line' => $faker->streetAddress,
                    'status' => $status === 'delivered' ? 'completed' : 'pending',
                ]);
                OrderStop::create([
                    'order_id' => $order->id,
                    'sequence' => 2,
                    'type' => 'delivery',
                    'city' => $deliveryCity,
                    'address_line' => $faker->streetAddress,
                    'status' => $status === 'delivered' ? 'completed' : 'pending',
                ]);
            }
        }

        // 6. Create loads for orders that don't have one
        $ordersWithoutLoads = Order::doesntHave('loads')->get();
        foreach ($ordersWithoutLoads as $idx => $order) {
            if ($idx % 5 == 0) continue; // leave some unassigned

            $loadStatuses = ['assigned', 'driver_accepted', 'en_route_pickup', 'at_pickup', 'loaded', 'en_route_delivery', 'at_delivery', 'delivered'];
            $status = $loadStatuses[array_rand($loadStatuses)];

            $driver = Driver::inRandomOrder()->first();
            $vehicle = $driver ? ($driver->current_vehicle_id ? Vehicle::find($driver->current_vehicle_id) : Vehicle::inRandomOrder()->first()) : Vehicle::inRandomOrder()->first();
            $assignedBy = $dispatcher->id;
            $assignedAt = Carbon::now()->subHours(rand(1, 12));
            $driverAcceptedAt = in_array($status, ['driver_accepted','en_route_pickup','at_pickup','loaded','en_route_delivery','at_delivery','delivered']) ? Carbon::now()->subHours(rand(1, 6)) : null;
            $etaAt = Carbon::now()->addHours(rand(1, 8));
            $isDelayed = ($status !== 'delivered' && rand(1,10) <= 2);
            if ($isDelayed) {
                $delayMinutes = rand(15, 90);
                $etaAt = Carbon::now()->addMinutes($delayMinutes);
            }
            $deliveredAt = ($status === 'delivered') ? Carbon::now()->subHours(rand(1, 24)) : null;

            Load::create([
                'load_number' => 'LD-DEMO-' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                'order_id' => $order->id,
                'driver_id' => $driver?->id,
                'vehicle_id' => $vehicle?->id,
                'assigned_by' => $assignedBy,
                'status' => $status,
                'assignment_type' => 'manual',
                'assigned_at' => $assignedAt,
                'driver_accepted_at' => $driverAcceptedAt,
                'eta_at' => $isDelayed ? $etaAt : null,
                'is_delayed' => $isDelayed,
                //'delay_minutes' => $isDelayed ? $delayMinutes : null,
                'delivered_at' => $deliveredAt,
                'created_at' => Carbon::now()->subDays(rand(0, 5)),
            ]);
        }

        $this->command->info('DispatchDemoSeeder completed. Added demo data without conflicts.');
    }
}
