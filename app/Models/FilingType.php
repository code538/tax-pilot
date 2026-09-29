<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilingType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'authority',
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

    // public function filings(): HasMany
    // {
    //     return $this->hasMany(Filing::class);
    // }
}