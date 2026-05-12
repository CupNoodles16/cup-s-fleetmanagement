<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get a dispatcher user (from UserSeeder)
        $dispatcher = User::role('dispatcher')->first();
        if (!$dispatcher) {
            $dispatcher = User::first();
        }

        if (!$dispatcher) {
            $this->command->error('No users found. Run UserSeeder first.');
            return;
        }

        // Get customers using the exact names from CustomerSeeder
        $mercado = Customer::where('company_name', 'Mercado Trading Co.')->first();
        $lim = Customer::where('company_name', 'Lim Hardware & Construction Supply')->first();
        $bukidnon = Customer::where('company_name', 'Bukidnon Fresh Produce Inc.')->first();
        $northern = Customer::where('company_name', 'Northern Mindanao Agri Traders')->first();

        if (!$mercado || !$lim || !$bukidnon || !$northern) {
            $this->command->error('Missing required customers. Run CustomerSeeder first.');
            return;
        }

        // --- Order 1: Draft ---
        $order1 = Order::firstOrCreate(
            ['order_number' => 'ORD-2026-0001'],
            [
                'customer_id'          => $mercado->id,
                'created_by'           => $dispatcher->id,
                'cargo_description'    => 'Assorted dry goods and grocery items',
                'cargo_type'           => 'general',
                'weight_kg'            => 1200,
                'volume_cbm'           => 5,
                'required_vehicle_type'=> 'closed_van',
                'status'               => 'draft',
                'priority'             => 'normal',
                'pickup_scheduled_at'  => now()->addDay()->setHour(7)->setMinute(0),
                'delivery_deadline_at' => now()->addDays(2)->setHour(12)->setMinute(0),
                'quoted_amount'        => 3500.00,
                'payment_status'       => 'unpaid',
                'tracking_token'       => Str::uuid(),
            ]
        );

        $this->createStops($order1, [
            [
                'sequence'      => 1,
                'type'          => 'pickup',
                'address_line'  => '123 Fortich Street, Brgy. 4',
                'city'          => 'Manolo Fortich',
                'province'      => 'Bukidnon',
                'lat'           => 8.3667,
                'lng'           => 124.8667,
                'contact_name'  => 'Ana Mercado',
                'contact_phone' => '09171112233',
                'scheduled_at'  => now()->addDay()->setHour(7)->setMinute(0),
                'status'        => 'pending',
            ],
            [
                'sequence'      => 2,
                'type'          => 'delivery',
                'address_line'  => '45 Aguinaldo Avenue',
                'city'          => 'Cagayan de Oro',
                'province'      => 'Misamis Oriental',
                'lat'           => 8.4820,
                'lng'           => 124.6490,
                'contact_name'  => 'Carlo Lim',
                'contact_phone' => '09182223344',
                'scheduled_at'  => now()->addDays(2)->setHour(10)->setMinute(0),
                'status'        => 'pending',
            ],
        ]);

        // --- Order 2: Confirmed ---
        $order2 = Order::firstOrCreate(
            ['order_number' => 'ORD-2026-0002'],
            [
                'customer_id'          => $lim->id,
                'created_by'           => $dispatcher->id,
                'cargo_description'    => 'Steel bars and cement bags',
                'cargo_type'           => 'general',
                'weight_kg'            => 4500,
                'volume_cbm'           => 12,
                'required_vehicle_type'=> 'flatbed',
                'status'               => 'confirmed',
                'priority'             => 'urgent',
                'pickup_scheduled_at'  => now()->addHours(3),
                'delivery_deadline_at' => now()->addHours(8),
                'confirmed_at'         => now()->subHour(),
                'quoted_amount'        => 7200.00,
                'payment_status'       => 'unpaid',
                'tracking_token'       => Str::uuid(),
            ]
        );

        $this->createStops($order2, [
            [
                'sequence'      => 1,
                'type'          => 'pickup',
                'address_line'  => '45 Aguinaldo Avenue',
                'city'          => 'Cagayan de Oro',
                'province'      => 'Misamis Oriental',
                'lat'           => 8.4820,
                'lng'           => 124.6490,
                'contact_name'  => 'Carlo Lim',
                'contact_phone' => '09182223344',
                'scheduled_at'  => now()->addHours(3),
                'status'        => 'pending',
            ],
            [
                'sequence'      => 2,
                'type'          => 'delivery',
                'address_line'  => '78 Pabayo Street',
                'city'          => 'Cagayan de Oro',
                'province'      => 'Misamis Oriental',
                'lat'           => 8.4793,
                'lng'           => 124.6518,
                'contact_name'  => 'Sophia Padilla',
                'contact_phone' => '09204445566',
                'scheduled_at'  => now()->addHours(6),
                'status'        => 'pending',
            ],
        ]);

        // --- Order 3: Multi-stop (perishable) ---
        $order3 = Order::firstOrCreate(
            ['order_number' => 'ORD-2026-0003'],
            [
                'customer_id'          => $bukidnon->id,
                'created_by'           => $dispatcher->id,
                'cargo_description'    => 'Fresh vegetables and fruits',
                'cargo_type'           => 'perishable',
                'weight_kg'            => 2000,
                'volume_cbm'           => 8,
                'required_vehicle_type'=> 'refrigerated',
                'status'               => 'confirmed',
                'priority'             => 'urgent',
                'pickup_scheduled_at'  => now()->addHours(2),
                'delivery_deadline_at' => now()->addHours(6),
                'confirmed_at'         => now()->subMinutes(30),
                'quoted_amount'        => 5800.00,
                'payment_status'       => 'unpaid',
                'tracking_token'       => Str::uuid(),
                'special_instructions' => 'Temperature must stay below 10°C',
            ]
        );

        $this->createStops($order3, [
            [
                'sequence'      => 1,
                'type'          => 'pickup',
                'address_line'  => 'Purok 3, Brgy. Kalasungay',
                'city'          => 'Malaybalay',
                'province'      => 'Bukidnon',
                'lat'           => 8.1575,
                'lng'           => 125.1278,
                'contact_name'  => 'Rodrigo Tan',
                'contact_phone' => '09193334455',
                'scheduled_at'  => now()->addHours(2),
                'status'        => 'pending',
            ],
            [
                'sequence'      => 2,
                'type'          => 'delivery',
                'address_line'  => 'Divisoria Market Stall 14',
                'city'          => 'Cagayan de Oro',
                'province'      => 'Misamis Oriental',
                'lat'           => 8.4780,
                'lng'           => 124.6440,
                'contact_name'  => 'Market Coordinator',
                'contact_phone' => '09193334466',
                'scheduled_at'  => now()->addHours(4),
                'status'        => 'pending',
            ],
            [
                'sequence'      => 3,
                'type'          => 'delivery',
                'address_line'  => '78 Pabayo Street',
                'city'          => 'Cagayan de Oro',
                'province'      => 'Misamis Oriental',
                'lat'           => 8.4793,
                'lng'           => 124.6518,
                'contact_name'  => 'Sophia Padilla',
                'contact_phone' => '09204445566',
                'scheduled_at'  => now()->addHours(5),
                'status'        => 'pending',
            ],
        ]);

        // --- Order 4: Delivered ---
        $order4 = Order::firstOrCreate(
            ['order_number' => 'ORD-2026-0004'],
            [
                'customer_id'          => $northern->id,
                'created_by'           => $dispatcher->id,
                'cargo_description'    => 'Sacks of rice and corn',
                'cargo_type'           => 'bulk',
                'weight_kg'            => 5500,
                'volume_cbm'           => 18,
                'required_vehicle_type'=> 'open_truck',
                'status'               => 'delivered',
                'priority'             => 'normal',
                'pickup_scheduled_at'  => now()->subDays(2),
                'delivery_deadline_at' => now()->subDay(),
                'confirmed_at'         => now()->subDays(2)->subHour(),
                'completed_at'         => now()->subDay()->addHours(2),
                'quoted_amount'        => 6000.00,
                'final_amount'         => 6000.00,
                'payment_status'       => 'invoiced',
                'tracking_token'       => Str::uuid(),
            ]
        );

        $this->createStops($order4, [
            [
                'sequence'      => 1,
                'type'          => 'pickup',
                'address_line'  => 'NFA Warehouse, Brgy. Bugo',
                'city'          => 'Cagayan de Oro',
                'province'      => 'Misamis Oriental',
                'lat'           => 8.5100,
                'lng'           => 124.6800,
                'contact_name'  => 'NFA Staff',
                'contact_phone' => '09193334455',
                'scheduled_at'  => now()->subDays(2),
                'arrived_at'    => now()->subDays(2)->addMinutes(15),
                'completed_at'  => now()->subDays(2)->addMinutes(45),
                'status'        => 'completed',
            ],
            [
                'sequence'      => 2,
                'type'          => 'delivery',
                'address_line'  => '78 Pabayo Street',
                'city'          => 'Cagayan de Oro',
                'province'      => 'Misamis Oriental',
                'lat'           => 8.4793,
                'lng'           => 124.6518,
                'contact_name'  => 'Sophia Padilla',
                'contact_phone' => '09204445566',
                'scheduled_at'  => now()->subDay(),
                'arrived_at'    => now()->subDay()->addHour(),
                'completed_at'  => now()->subDay()->addHours(2),
                'status'        => 'completed',
            ],
        ]);

        $this->command->info('OrderSeeder completed successfully.');
    }

    private function createStops(Order $order, array $stops): void
    {
        foreach ($stops as $stop) {
            OrderStop::updateOrCreate(
                ['order_id' => $order->id, 'sequence' => $stop['sequence']],
                array_merge($stop, ['order_id' => $order->id])
            );
        }
    }
}
