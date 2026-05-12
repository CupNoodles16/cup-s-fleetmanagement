<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'email'             => 'ramon@truckdispatch.test',
                'plate_number'      => 'ABC-1234',
                'license_number'    => 'N01-23-456789',
                'license_type'      => 'professional',
                'license_expiry'    => '2027-06-30',
                'status'            => 'available',
                'hos_remaining_minutes' => 600,
                'last_lat'          => 8.4793,
                'last_lng'          => 124.6518,
                'performance_rating'=> 4.85,
                'total_deliveries'  => 142,
                'phone'             => '09171234567',
                'emergency_contact' => 'Luisa Dela Cruz',
                'emergency_phone'   => '09179876543',
            ],
            [
                'email'             => 'eduardo@truckdispatch.test',
                'plate_number'      => 'DEF-5678',
                'license_number'    => 'N01-23-567890',
                'license_type'      => 'professional',
                'license_expiry'    => '2026-09-15',
                'status'            => 'available',
                'hos_remaining_minutes' => 480,
                'last_lat'          => 8.4942,
                'last_lng'          => 124.6573,
                'performance_rating'=> 4.60,
                'total_deliveries'  => 98,
                'phone'             => '09182345678',
                'emergency_contact' => 'Rosario Villanueva',
                'emergency_phone'   => '09183456789',
            ],
            [
                'email'             => 'danilo@truckdispatch.test',
                'plate_number'      => 'GHI-9012',
                'license_number'    => 'N01-23-678901',
                'license_type'      => 'professional',
                'license_expiry'    => '2027-03-22',
                'status'            => 'available',
                'hos_remaining_minutes' => 600,
                'last_lat'          => 8.4550,
                'last_lng'          => 124.6300,
                'performance_rating'=> 4.90,
                'total_deliveries'  => 210,
                'phone'             => '09193456789',
                'emergency_contact' => 'Teresa Bautista',
                'emergency_phone'   => '09194567890',
            ],
            [
                'email'             => 'fernando@truckdispatch.test',
                'plate_number'      => null, // vehicle in maintenance, no assignment
                'license_number'    => 'N01-23-789012',
                'license_type'      => 'professional',
                'license_expiry'    => '2026-12-01',
                'status'            => 'off_duty',
                'hos_remaining_minutes' => 600,
                'last_lat'          => null,
                'last_lng'          => null,
                'performance_rating'=> 4.40,
                'total_deliveries'  => 55,
                'phone'             => '09201234567',
                'emergency_contact' => 'Carla Aquino',
                'emergency_phone'   => '09202345678',
            ],
        ];

        foreach ($drivers as $data) {
            $user = User::where('email', $data['email'])->first();
            if (!$user) continue;

            $vehicle = $data['plate_number']
                ? Vehicle::where('plate_number', $data['plate_number'])->first()
                : null;

            Driver::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'current_vehicle_id'    => $vehicle?->id,
                    'license_number'        => $data['license_number'],
                    'license_type'          => $data['license_type'],
                    'license_expiry'        => $data['license_expiry'],
                    'status'                => $data['status'],
                    'hos_remaining_minutes' => $data['hos_remaining_minutes'],
                    'last_lat'              => $data['last_lat'],
                    'last_lng'              => $data['last_lng'],
                    'last_location_at'      => $data['last_lat'] ? now()->subMinutes(rand(2, 15)) : null,
                    'performance_rating'    => $data['performance_rating'],
                    'total_deliveries'      => $data['total_deliveries'],
                    'phone'                 => $data['phone'],
                    'emergency_contact'     => $data['emergency_contact'],
                    'emergency_phone'       => $data['emergency_phone'],
                ]
            );
        }
    }
}
