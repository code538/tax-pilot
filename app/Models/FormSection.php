<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_version_id',
        'name',
        'slug',
        'description',
        'sort_order',
        'is_repeatable',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_repeatable' => 'boolean',
        ];
    }

    public function formVersion(): BelongsTo
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class);
    }
}