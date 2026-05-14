<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Support\WidgetApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ApiTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.api.token' => 'test-token']);
    }

    public function test_api_requires_token(): void
    {
        $this->postJson('/api/tickets', [])->assertUnauthorized();
    }

    public function test_ticket_can_be_created_from_api(): void
    {
        $response = $this
            ->withToken('test-token')
            ->post('/api/tickets', [
                'customer' => [
                    'name' => 'John Smith',
                    'phone' => '+380501112233',
                    'email' => 'john@example.test',
                ],
                'subject' => 'Payment issue',
                'message' => 'Customer cannot complete payment.',
                'attachments' => [
                    UploadedFile::fake()->image('proof.jpg'),
                ],
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', Ticket::STATUS_NEW)
            ->assertJsonPath('data.customer.phone', '+380501112233')
            ->assertJsonPath('data.attachments_count', 1);

        $this->assertDatabaseHas('customers', [
            'phone' => '+380501112233',
            'email' => 'john@example.test',
        ]);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Payment issue',
            'status' => Ticket::STATUS_NEW,
        ]);
    }

    public function test_ticket_can_be_created_from_widget_token(): void
    {
        $response = $this
            ->withHeader('X-Widget-Token', WidgetApiToken::make(now()->addHour()->timestamp))
            ->post('/api/tickets', [
                'customer' => [
                    'name' => 'Widget User',
                    'phone' => '+380671234500',
                    'email' => 'widget@example.test',
                ],
                'subject' => 'Widget request',
                'message' => 'Request created from embedded widget.',
                'attachments' => [
                    UploadedFile::fake()->create('brief.txt', 20, 'text/plain'),
                ],
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.subject', 'Widget request')
            ->assertJsonPath('data.attachments_count', 1);
    }

    public function test_api_rejects_disallowed_attachment_type(): void
    {
        $response = $this
            ->withToken('test-token')
            ->withHeader('Accept', 'application/json')
            ->post('/api/tickets', [
                'customer' => [
                    'name' => 'Unsafe File User',
                    'phone' => '+380671234501',
                ],
                'subject' => 'Bad attachment',
                'message' => 'Trying to upload an unsupported file.',
                'attachments' => [
                    UploadedFile::fake()->create('script.exe', 10, 'application/octet-stream'),
                ],
            ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid(['attachments.0']);
    }

    public function test_ticket_statistics_are_returned_for_day_week_and_month(): void
    {
        $customer = Customer::factory()->create();

        Ticket::factory()->for($customer)->create([
            'status' => Ticket::STATUS_NEW,
            'created_at' => now()->subHours(2),
        ]);

        Ticket::factory()->for($customer)->create([
            'status' => Ticket::STATUS_PROCESSED,
            'created_at' => now()->subDays(3),
        ]);

        Ticket::factory()->for($customer)->create([
            'status' => Ticket::STATUS_IN_PROGRESS,
            'created_at' => now()->subDays(20),
        ]);

        $this
            ->withToken('test-token')
            ->getJson('/api/tickets/statistics')
            ->assertOk()
            ->assertJsonPath('data.day.total', 1)
            ->assertJsonPath('data.week.total', 2)
            ->assertJsonPath('data.month.total', 3)
            ->assertJsonPath('data.month.by_status.new', 1)
            ->assertJsonPath('data.month.by_status.in_progress', 1)
            ->assertJsonPath('data.month.by_status.processed', 1);
    }
}
