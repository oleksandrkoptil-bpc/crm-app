<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ManagerTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_tickets(): void
    {
        $manager = $this->manager();
        $ticket = Ticket::factory()->for(Customer::factory())->create([
            'subject' => 'Payment issue',
        ]);

        $this->actingAs($manager)
            ->get(route('manager.tickets.index'))
            ->assertOk()
            ->assertSee($ticket->subject);
    }

    public function test_manager_can_update_ticket_status(): void
    {
        $manager = $this->manager();
        $ticket = Ticket::factory()->for(Customer::factory())->create([
            'status' => Ticket::STATUS_NEW,
            'manager_replied_at' => null,
        ]);

        $this->actingAs($manager)
            ->patch(route('manager.tickets.update-status', $ticket), [
                'status' => Ticket::STATUS_PROCESSED,
            ])
            ->assertRedirect();

        $ticket->refresh();

        $this->assertSame(Ticket::STATUS_PROCESSED, $ticket->status);
        $this->assertNotNull($ticket->manager_replied_at);
    }

    public function test_guest_cannot_open_manager_tickets(): void
    {
        $this->get(route('manager.tickets.index'))->assertRedirect(route('login'));
    }

    private function manager(): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::firstOrCreate(['name' => 'manager']);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
