<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class TicketSubmissionLimiter
{
    private const TTL_SECONDS = 86400;

    public function acquire(array $customer): bool
    {
        $reservedKeys = [];

        foreach ($this->keysFor($customer) as $key) {
            if (! Cache::add($key, true, now()->addSeconds(self::TTL_SECONDS))) {
                foreach ($reservedKeys as $reservedKey) {
                    Cache::forget($reservedKey);
                }

                return false;
            }

            $reservedKeys[] = $key;
        }

        return true;
    }

    public function release(array $customer): void
    {
        foreach ($this->keysFor($customer) as $key) {
            Cache::forget($key);
        }
    }

    private function keysFor(array $customer): array
    {
        $keys = [
            $this->phoneKey($customer['phone']),
        ];

        if (! empty($customer['email'])) {
            $keys[] = $this->emailKey($customer['email']);
        }

        return $keys;
    }

    private function phoneKey(string $phone): string
    {
        return 'ticket_submission_limit:phone:'.sha1($phone);
    }

    private function emailKey(string $email): string
    {
        return 'ticket_submission_limit:email:'.sha1(mb_strtolower($email));
    }
}
