<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class TicketStatisticsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'day' => $this->statsFor(Carbon::now()->subDay()),
                'week' => $this->statsFor(Carbon::now()->subWeek()),
                'month' => $this->statsFor(Carbon::now()->subMonth()),
            ],
        ]);
    }

    private function statsFor(Carbon $from): array
    {
        $query = Ticket::query()->createdFrom($from);

        return [
            'total' => (clone $query)->count(),
            'by_status' => [
                Ticket::STATUS_NEW => (clone $query)->withStatus(Ticket::STATUS_NEW)->count(),
                Ticket::STATUS_IN_PROGRESS => (clone $query)->withStatus(Ticket::STATUS_IN_PROGRESS)->count(),
                Ticket::STATUS_PROCESSED => (clone $query)->withStatus(Ticket::STATUS_PROCESSED)->count(),
            ],
        ];
    }
}
