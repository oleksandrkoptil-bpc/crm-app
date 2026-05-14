<?php

namespace App\Http\Middleware;

use App\Support\WidgetApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = config('services.api.token');
        $bearerToken = (string) $request->bearerToken();

        if ($token && hash_equals($token, $bearerToken)) {
            return $next($request);
        }

        if (
            $request->isMethod('post')
            && $request->is('api/tickets')
            && WidgetApiToken::isValid($request->header('X-Widget-Token'))
        ) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
    }
}
