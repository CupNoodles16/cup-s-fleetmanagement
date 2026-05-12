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
            ['email' => 'superadmin@truckdispatch.test'],
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
            ['name' => 'Maria Santos',  'email' => 'maria@truckdispatch.test'],
            ['name' => 'Jose Reyes',    'email' => 'jose@truckdispatch.test'],
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
            ['name' => 'Ramon Dela Cruz',   'email' => 'ramon@truckdispatch.test'],
            ['name' => 'Eduardo Villanueva','email' => 'eduardo@truckdispatch.test'],
            ['name' => 'Danilo Bautista',   'email' => 'danilo@truckdispatch.test'],
            ['name' => 'Fernando Aquino',   'email' => 'fernando@truckdispatch.test'],
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
            ['name' => 'Ana Mercado',   'email' => 'ana@testcustomer.test'],
            ['name' => 'Carlo Lim',     'email' => 'carlo@testcustomer.test'],
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
