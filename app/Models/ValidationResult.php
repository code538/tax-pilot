<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidationResult extends Model
{
    protected $fillable = [
        'filing_id',
        'filing_version_id',
        'status',
        'error_count',
        'warning_count',
        'errors',
        'warnings',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'error_count' => 'integer',
        'warning_count' => 'integer',
        'errors' => 'array',
        'warnings' => 'array',
        'validated_at' => 'datetime',
    ];

    public function filing(): BelongsTo
    {
        return $this->belongsTo(Filing::class);
    }

    public function filingVersion(): BelongsTo
    {
        return $this->belongsTo(FilingVersion::class);
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}