<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            // Customer with portal access
            [
                'email'           => 'ana@testcustomer.test',
                'company_name'    => 'Mercado Trading Co.',
                'contact_person'  => 'Ana Mercado',
                'phone'           => '09171112233',
                'billing_address' => '123 Fortich Street, Brgy. 4',
                'billing_city'    => 'Manolo Fortich',
                'billing_province'=> 'Bukidnon',
                'billing_zip'     => '8703',
                'status'          => 'active',
                'credit_limit'    => 50000.00,
                'notes'           => 'Preferred pickup before 8AM.',
            ],
            [
                'email'           => 'carlo@testcustomer.test',
                'company_name'    => 'Lim Hardware & Construction Supply',
                'contact_person'  => 'Carlo Lim',
                'phone'           => '09182223344',
                'billing_address' => '45 Aguinaldo Avenue',
                'billing_city'    => 'Cagayan de Oro',
                'billing_province'=> 'Misamis Oriental',
                'billing_zip'     => '9000',
                'status'          => 'active',
                'credit_limit'    => 150000.00,
                'notes'           => 'Flatbed preferred for construction materials.',
            ],
            // Walk-in / phone-in customers without portal accounts
            [
                'email'           => null,
                'company_name'    => 'Bukidnon Fresh Produce Inc.',
                'contact_person'  => 'Rodrigo Tan',
                'phone'           => '09193334455',
                'billing_address' => 'Purok 3, Brgy. Kalasungay',
                'billing_city'    => 'Malaybalay',
                'billing_province'=> 'Bukidnon',
                'billing_zip'     => '8700',
                'status'          => 'active',
                'credit_limit'    => 30000.00,
                'notes'           => 'Perishable cargo — refrigerated truck required.',
            ],
            [
                'email'           => null,
                'company_name'    => 'Northern Mindanao Agri Traders',
                'contact_person'  => 'Sophia Padilla',
                'phone'           => '09204445566',
                'billing_address' => '78 Pabayo Street',
                'billing_city'    => 'Cagayan de Oro',
                'billing_province'=> 'Misamis Oriental',
                'billing_zip'     => '9000',
                'status'          => 'active',
                'credit_limit'    => 75000.00,
            ],
        ];

        foreach ($customers as $data) {
            $user = $data['email']
                ? User::where('email', $data['email'])->first()
                : null;

            Customer::firstOrCreate(
                ['company_name' => $data['company_name']],
                [
                    'user_id'          => $user?->id,
                    'company_name'     => $data['company_name'],
                    'contact_person'   => $data['contact_person'],
                    'phone'            => $data['phone'],
                    'email'            => $data['email'],
                    'billing_address'  => $data['billing_address'],
                    'billing_city'     => $data['billing_city'],
                    'billing_province' => $data['billing_province'],
                    'billing_zip'      => $data['billing_zip'],
                    'status'           => $data['status'],
                    'credit_limit'     => $data['credit_limit'],
                    'notes'            => $data['notes'] ?? null,
                ]
            );
        }
    }
}
