<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlanFilingType extends Model
{
    protected $table = 'subscription_plan_filing_types';

    protected $fillable = [
        'subscription_plan_id',
        'filing_type_id',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionPlan::class,
            'subscription_plan_id'
        );
    }

    public function filingType(): BelongsTo
    {
        return $this->belongsTo(
            FilingType::class,
            'filing_type_id'
        );
    }
}