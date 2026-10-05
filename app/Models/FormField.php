<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_section_id',
        'field_key',
        'label',
        'field_type',
        'placeholder',
        'help_text',
        'is_required',
        'options',
        'validation_rules',
        'external_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'options' => 'array',
            'validation_rules' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'form_section_id');
    }
}