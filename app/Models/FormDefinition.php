<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormDefinition extends Model
{
     use HasFactory;

    protected $fillable = [
        'filing_type_id',
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

    public function filingType(): BelongsTo
    {
        return $this->belongsTo(FilingType::class);
    }

    

    public function versions(): HasMany
    {
        return $this->hasMany(FormVersion::class);
    }
}
