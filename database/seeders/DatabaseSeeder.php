<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin CRM',
            'email' => 'admin@crm.test',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Manager CRM',
            'email' => 'manager@crm.test',
            'role' => 'manager',
            'password' => Hash::make('password'),
        ]);

        $customers = Customer::factory(6)->create();

        Ticket::factory(12)->recycle($customers)->create();
    }
}
