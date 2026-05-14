<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTicketRequest;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
    #[OA\Post(
        path: '/api/tickets',
        tags: ['Tickets'],
        summary: 'Create a ticket',
        description: 'Creates a new ticket and attaches uploaded files when provided.',
        security: [['bearerAuth' => []], ['widgetToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['customer[name]', 'customer[phone]', 'subject', 'message'],
                    properties: [
                        new OA\Property(property: 'customer[name]', type: 'string', example: 'John Smith'),
                        new OA\Property(property: 'customer[phone]', type: 'string', example: '+380501112233'),
                        new OA\Property(property: 'customer[email]', type: 'string', format: 'email', nullable: true, example: 'john@example.test'),
                        new OA\Property(property: 'subject', type: 'string', example: 'Payment issue'),
                        new OA\Property(property: 'message', type: 'string', example: 'Customer cannot complete payment.'),
                        new OA\Property(property: 'status', type: 'string', enum: ['new', 'in_progress', 'processed'], example: 'new'),
                        new OA\Property(
                            property: 'attachments[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                        ),
                    ],
                    type: 'object',
                ),
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Ticket created successfully',
                content: new OA\JsonContent(ref: '#/components/schemas/TicketStoreResponse'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(ref: '#/components/schemas/UnauthorizedResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $data = $request->validated();

        $customer = Customer::query()->updateOrCreate(
            ['phone' => $data['customer']['phone']],
            [
                'name' => $data['customer']['name'],
                'email' => $data['customer']['email'] ?? null,
            ],
        );

        $ticket = Ticket::query()->create([
            'customer_id' => $customer->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => $data['status'] ?? Ticket::STATUS_NEW,
        ]);

        foreach ($request->file('attachments', []) as $file) {
            $ticket->addMedia($file)->toMediaCollection('attachments');
        }

        Cache::forget('ticket_statistics');

        $ticket->load('customer');

        return response()->json([
            'data' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'message' => $ticket->message,
                'status' => $ticket->status,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                ],
                'attachments_count' => $ticket->getMedia('attachments')->count(),
                'created_at' => $ticket->created_at?->toISOString(),
            ],
        ], Response::HTTP_CREATED);
    }
}
