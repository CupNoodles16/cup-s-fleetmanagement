<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@cupnoodles.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('password'),
                'role'              => 'superadmin',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        $superadmin->assignRole('superadmin');

        // Dispatchers
        $dispatchers = [
            ['name' => 'Maria Santos',  'email' => 'maria@truckdispatch.com'],
            ['name' => 'Jose Reyes',    'email' => 'jose@truckdispatch.com'],
        ];

        foreach ($dispatchers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'dispatcher',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('dispatcher');
        }

        // Drivers — corresponding Driver records created in DriverSeeder
        $drivers = [
            ['name' => 'Ramon Dela Cruz',   'email' => 'ramon@truckdispatch.com'],
            ['name' => 'Eduardo Villanueva','email' => 'eduardo@truckdispatch.com'],
            ['name' => 'Danilo Bautista',   'email' => 'danilo@truckdispatch.com'],
            ['name' => 'Fernando Aquino',   'email' => 'fernando@truckdispatch.com'],
        ];

        foreach ($drivers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'driver',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('driver');
        }

        // Customers — corresponding Customer records created in CustomerSeeder
        $customers = [
            ['name' => 'Ana Mercado',   'email' => 'ana@testcustomer.com'],
            ['name' => 'Carlo Lim',     'email' => 'carlo@testcustomer.com'],
        ];

        foreach ($customers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'role'              => 'customer',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('customer');
        }
    }
}
