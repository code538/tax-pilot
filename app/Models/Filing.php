<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Filing extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'filing_subject_id',
        'filing_type_id',
        'reference',
        'status',
        'prepared_by',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class
        );
    }

    public function filingSubject(): BelongsTo
    {
        return $this->belongsTo(
            FilingSubject::class
        );
    }

    public function filingType(): BelongsTo
    {
        return $this->belongsTo(
            FilingType::class
        );
    }

    public function preparer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'prepared_by'
        );
    }

    public function versions(): HasMany
    {
        return $this->hasMany(
            FilingVersion::class
        );
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(FilingVersion::class)
            ->latestOfMany('version_number');
    }
}