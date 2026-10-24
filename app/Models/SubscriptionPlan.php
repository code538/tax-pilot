<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plans';

    protected $fillable = [
        'subscription_product_id',
        'name',
        'slug',
        'description',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionProduct::class,
            'subscription_product_id'
        );
    }

    public function prices(): HasMany
    {
        return $this->hasMany(
            SubscriptionPlanPrice::class,
            'subscription_plan_id'
        );
    }

    public function activePrices(): HasMany
    {
        return $this->prices()->where('is_active', true);
    }

    public function filingTypeMappings(): HasMany
    {
        return $this->hasMany(
            SubscriptionPlanFilingType::class,
            'subscription_plan_id'
        );
    }

    public function filingTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            FilingType::class,
            'subscription_plan_filing_types',
            'subscription_plan_id',
            'filing_type_id'
        )->withTimestamps();
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(
            SubscriptionPlanEntitlement::class,
            'subscription_plan_id'
        );
    }

    public function organisationSubscriptions(): HasMany
    {
        return $this->hasMany(
            OrganisationSubscription::class,
            'subscription_plan_id'
        );
    }
}