<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionProduct extends Model
{
    protected $table = 'subscription_products';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function plans(): HasMany
    {
        return $this->hasMany(
            SubscriptionPlan::class,
            'subscription_product_id'
        )->orderBy('sort_order');
    }

    public function activePlans(): HasMany
    {
        return $this->plans()->where('is_active', true);
    }
}