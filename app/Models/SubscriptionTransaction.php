<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionTransaction extends Model
{
    protected $table = 'subscription_transactions';

    protected $fillable = [
        'organisation_subscription_id',
        'provider',
        'provider_transaction_id',
        'amount_minor',
        'currency',
        'status',
        'transaction_type',
        'paid_at',
        'refunded_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'amount_minor' => 'integer',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function organisationSubscription(): BelongsTo
    {
        return $this->belongsTo(
            OrganisationSubscription::class,
            'organisation_subscription_id'
        );
    }
}