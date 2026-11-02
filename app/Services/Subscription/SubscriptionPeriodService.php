<?php

namespace App\Services\Subscription;

use Carbon\Carbon;
use InvalidArgumentException;

class SubscriptionPeriodService
{
    public function periodEnd(
        Carbon $periodStart,
        string $billingInterval,
        int $intervalCount = 1
    ): Carbon {
        if ($intervalCount < 1) {
            throw new InvalidArgumentException(
                'Billing interval count must be at least 1.'
            );
        }

        return match ($billingInterval) {
            'daily' => $periodStart->copy()->addDays($intervalCount),
            'weekly' => $periodStart->copy()->addWeeks($intervalCount),
            'monthly' => $periodStart->copy()->addMonthsNoOverflow($intervalCount),
            'quarterly' => $periodStart->copy()->addMonthsNoOverflow(3 * $intervalCount),
            'yearly' => $periodStart->copy()->addYearsNoOverflow($intervalCount),

            default => throw new InvalidArgumentException(
                "Unsupported billing interval: {$billingInterval}"
            ),
        };
    }
}