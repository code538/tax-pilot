<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganisationSubscription extends Model
{
    protected $table = 'organisation_subscriptions';

    protected $fillable = [
        'organisation_id',
        'subscription_plan_id',
        'subscription_plan_price_id',
        'status',
        'starts_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'ends_at',
        'cancelled_at',
        'provider',
        'provider_customer_id',
        'provider_subscription_id',
        'amount_minor',
        'currency',
        'billing_interval',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'current_period_starts_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'amount_minor' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class,
            'organisation_id'
        );
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionPlan::class,
            'subscription_plan_id'
        );
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionPlanPrice::class,
            'subscription_plan_price_id'
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(
            SubscriptionTransaction::class,
            'organisation_subscription_id'
        );
    }

    public function isCurrentlyActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (
            $this->starts_at !== null &&
            $this->starts_at->isFuture()
        ) {
            return false;
        }

        if (
            $this->current_period_ends_at !== null &&
            $this->current_period_ends_at->isPast()
        ) {
            return false;
        }

        return true;
    }
}