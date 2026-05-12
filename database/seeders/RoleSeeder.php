<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Permissions ---
        $permissions = [
            // Orders
            'order.view',
            'order.create',
            'order.edit',
            'order.cancel',
            'order.confirm',

            // Loads
            'load.view',
            'load.assign',
            'load.reassign',
            'load.cancel',
            'load.update_status',

            // Drivers
            'driver.view',
            'driver.create',
            'driver.edit',
            'driver.suspend',
            'driver.verify_documents',

            // Vehicles
            'vehicle.view',
            'vehicle.create',
            'vehicle.edit',
            'vehicle.retire',

            // Customers
            'customer.view',
            'customer.create',
            'customer.edit',
            'customer.blacklist',

            // Invoices
            'invoice.view',
            'invoice.create',
            'invoice.mark_paid',

            // Reports
            'report.view',

            // Users
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // --- Roles ---

        // Superadmin — no permission gates, bypasses all checks
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        // Dispatcher — day-to-day dispatch operations
        $dispatcher = Role::firstOrCreate(['name' => 'dispatcher', 'guard_name' => 'web']);
        $dispatcher->syncPermissions([
            'order.view', 'order.create', 'order.edit', 'order.cancel', 'order.confirm',
            'load.view', 'load.assign', 'load.reassign', 'load.cancel', 'load.update_status',
            'driver.view',
            'vehicle.view',
            'customer.view', 'customer.create', 'customer.edit',
            'invoice.view', 'invoice.create',
            'report.view',
        ]);

        // Driver — mobile app only, very limited scope
        $driver = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);
        $driver->syncPermissions([
            'load.view',
            'load.update_status',
            'order.view',
        ]);

        // Customer — portal access, own orders only (enforced at controller level)
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $customer->syncPermissions([
            'order.view',
            'order.create',
            'invoice.view',
        ]);
    }
}
