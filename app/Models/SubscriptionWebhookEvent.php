<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionWebhookEvent extends Model
{
    protected $table = 'subscription_webhook_events';

    protected $fillable = [
        'provider',
        'event_id',
        'event_type',
        'status',
        'processed_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }
}