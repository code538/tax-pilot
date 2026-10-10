<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilingFieldValue extends Model
{
    protected $fillable = [
        'filing_id',
        'filing_version_id',
        'form_field_id',
        'value',
    ];

    protected $casts = [
        'filing_id' => 'integer',
        'filing_version_id' => 'integer',
        'form_field_id' => 'integer',
    ];

    public function filing(): BelongsTo
    {
        return $this->belongsTo(Filing::class);
    }

    public function filingVersion(): BelongsTo
    {
        return $this->belongsTo(FilingVersion::class);
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }
}