<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTicketRequest;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
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
