<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'plate_number'          => 'ABC-1234',
                'model'                 => 'Isuzu Elf',
                'year'                  => 2021,
                'type'                  => 'closed_van',
                'capacity_kg'           => 3000,
                'status'                => 'available',
                'registration_expiry'   => '2026-12-31',
                'insurance_expiry'      => '2026-12-31',
                'last_maintenance_date' => '2026-03-15',
                'odometer_km'           => 45200,
            ],
            [
                'plate_number'          => 'DEF-5678',
                'model'                 => 'Mitsubishi Fuso Canter',
                'year'                  => 2020,
                'type'                  => 'open_truck',
                'capacity_kg'           => 5000,
                'status'                => 'available',
                'registration_expiry'   => '2026-11-30',
                'insurance_expiry'      => '2026-11-30',
                'last_maintenance_date' => '2026-02-20',
                'odometer_km'           => 78400,
            ],
            [
                'plate_number'          => 'GHI-9012',
                'model'                 => 'Hino 300',
                'year'                  => 2022,
                'type'                  => 'refrigerated',
                'capacity_kg'           => 2500,
                'status'                => 'available',
                'registration_expiry'   => '2027-01-31',
                'insurance_expiry'      => '2027-01-31',
                'last_maintenance_date' => '2026-04-01',
                'odometer_km'           => 21300,
            ],
            [
                'plate_number'          => 'JKL-3456',
                'model'                 => 'Isuzu Forward',
                'year'                  => 2019,
                'type'                  => 'flatbed',
                'capacity_kg'           => 8000,
                'status'                => 'in_maintenance',
                'registration_expiry'   => '2026-09-30',
                'insurance_expiry'      => '2026-09-30',
                'last_maintenance_date' => '2026-04-28',
                'odometer_km'           => 132000,
                'notes'                 => 'Brake pads replacement in progress.',
            ],
            [
                'plate_number'          => 'MNO-7890',
                'model'                 => 'Mitsubishi Fuso Fighter',
                'year'                  => 2020,
                'type'                  => 'closed_van',
                'capacity_kg'           => 6000,
                'status'                => 'available',
                'registration_expiry'   => '2026-10-31',
                'insurance_expiry'      => '2026-10-31',
                'last_maintenance_date' => '2026-03-01',
                'odometer_km'           => 89500,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(
                ['plate_number' => $vehicle['plate_number']],
                $vehicle
            );
        }
    }
}
