<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\FormVersion;
use App\Models\Filing;
use App\Models\Organisation;
use App\Models\User;

class FilingVersion extends Model
{
    protected $fillable = [
        'filing_id',
        'organisation_id',
        'form_version_id',
        'version_number',
        'supersedes_version_id',
        'revision_reason',
        'prepared_by',
        'prepared_at',
        'locked_at',
        'content_hash',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'prepared_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function filing(): BelongsTo
    {
        return $this->belongsTo(Filing::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function formVersion(): BelongsTo
    {
        return $this->belongsTo(FormVersion::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * The previous filing version that this version replaces.
     */
    public function supersedesVersion(): BelongsTo
    {
        return $this->belongsTo(
            FilingVersion::class,
            'supersedes_version_id'
        );
    }
}