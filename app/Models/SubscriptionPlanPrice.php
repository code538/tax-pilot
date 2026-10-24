<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlanPrice extends Model
{
    protected $table = 'subscription_plan_prices';

    protected $fillable = [
        'subscription_plan_id',
        'billing_interval',
        'interval_count',
        'amount_minor',
        'currency',
        'stripe_product_id',
        'stripe_price_id',
        'is_active',
        'effective_from',
        'effective_until',
    ];

    protected function casts(): array
    {
        return [
            'interval_count' => 'integer',
            'amount_minor' => 'integer',
            'is_active' => 'boolean',
            'effective_from' => 'datetime',
            'effective_until' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionPlan::class,
            'subscription_plan_id'
        );
    }

    public function organisationSubscriptions(): HasMany
    {
        return $this->hasMany(
            OrganisationSubscription::class,
            'subscription_plan_price_id'
        );
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format(
            $this->amount_minor / 100,
            2
        );
    }
}