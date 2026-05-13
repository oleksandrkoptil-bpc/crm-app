<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement([
            Ticket::STATUS_NEW,
            Ticket::STATUS_IN_PROGRESS,
            Ticket::STATUS_PROCESSED,
        ]);

        return [
            'customer_id' => Customer::factory(),
            'subject' => fake()->randomElement([
                'Payment issue',
                'Need a consultation',
                'Confirmation email not received',
                'Request to update account details',
            ]),
            'message' => fake()->paragraphs(2, true),
            'status' => $status,
            'manager_replied_at' => $status === Ticket::STATUS_NEW
                ? null
                : fake()->dateTimeBetween('-5 days'),
        ];
    }
}
