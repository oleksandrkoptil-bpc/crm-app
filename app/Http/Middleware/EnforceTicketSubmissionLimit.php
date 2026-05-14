<?php

namespace App\Http\Middleware;

use App\Support\TicketSubmissionLimiter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceTicketSubmissionLimit
{
    public function __construct(
        private readonly TicketSubmissionLimiter $limiter
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $customer = (array) $request->input('customer', []);

        if (empty($customer['phone']) && empty($customer['email'])) {
            return $next($request);
        }

        if (! $this->limiter->acquire($customer)) {
            return response()->json([
                'message' => 'A ticket has already been submitted today with this phone number or email.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        try {
            return $next($request);
        } catch (\Throwable $exception) {
            $this->limiter->release($customer);

            throw $exception;
        }
    }
}
