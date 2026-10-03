<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilingSubject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organisation_id',
        'subject_type',
        'name',
        'company_number',
        'company_type',
        'utr_number',
        'vat_number',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'companies_house_status',
        'incorporation_date',
        'dissolution_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'incorporation_date' => 'date',
            'dissolution_date' => 'date',
        ];
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(
            Organisation::class
        );
    }

    public function filings()
    {
        return $this->hasMany(Filing::class);
    }
}