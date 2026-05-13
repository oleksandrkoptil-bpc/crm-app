<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $managerRole = Role::firstOrCreate(['name' => 'manager']);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@crm.test'],
            [
                'name' => 'Admin CRM',
                'password' => Hash::make('password'),
            ],
        );

        $manager = User::query()->updateOrCreate(
            ['email' => 'manager@crm.test'],
            [
                'name' => 'Manager CRM',
                'password' => Hash::make('password'),
            ],
        );

        $admin->assignRole($adminRole);
        $manager->assignRole($managerRole);

        $customers = Customer::factory(6)->create();

        Ticket::factory(12)->recycle($customers)->create();
    }
}
