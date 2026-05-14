<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_home_page(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_authenticated_manager_is_redirected_to_tickets_from_home_page(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'manager']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('manager.tickets.index'));
    }
}
