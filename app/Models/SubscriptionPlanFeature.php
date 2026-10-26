<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlanFeature extends Model
{
    protected $table = 'subscription_plan_features';

    protected $fillable = [
        'subscription_plan_id',
        'label',
        'is_included',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_included' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionPlan::class,
            'subscription_plan_id'
        );
    }
}
