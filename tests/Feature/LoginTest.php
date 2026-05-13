<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_log_in(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'manager']);
        $user = User::factory()->create([
            'email' => 'manager@crm.test',
            'password' => 'password',
        ]);
        $user->assignRole($role);

        $this->post(route('login.store'), [
            'email' => 'manager@crm.test',
            'password' => 'password',
        ])->assertRedirect(route('manager.tickets.index'));

        $this->assertAuthenticatedAs($user);
    }
}
