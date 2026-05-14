<?php

namespace App\Support;

class WidgetApiToken
{
    public static function make(?int $expiresAt = null): string
    {
        $expiresAt ??= now()->addMinutes((int) config('services.widget.token_ttl', 60))->timestamp;

        $signature = hash_hmac('sha256', "widget|{$expiresAt}", (string) config('app.key'));

        return "{$expiresAt}.{$signature}";
    }

    public static function isValid(?string $token): bool
    {
        if (! $token || ! str_contains($token, '.')) {
            return false;
        }

        [$expiresAt, $signature] = explode('.', $token, 2);

        if (! ctype_digit($expiresAt) || (int) $expiresAt < now()->timestamp) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', "widget|{$expiresAt}", (string) config('app.key'));

        return hash_equals($expectedSignature, $signature);
    }
}
