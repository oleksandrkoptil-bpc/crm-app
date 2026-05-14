<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class TicketStatisticsController extends Controller
{
    private const CACHE_KEY = 'ticket_statistics';
    private const CACHE_TTL_MINUTES = 5;

    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => Cache::remember(
                self::CACHE_KEY,
                now()->addMinutes(self::CACHE_TTL_MINUTES),
                fn () => $this->statistics()
            ),
        ]);
    }

    private function statistics(): array
    {
        $now = Carbon::now();

        return [
            'day' => $this->statsFor($now->copy()->subDay()),
            'week' => $this->statsFor($now->copy()->subWeek()),
            'month' => $this->statsFor($now->copy()->subMonth()),
        ];
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
