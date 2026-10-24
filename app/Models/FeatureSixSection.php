<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeatureSixSection extends Model
{
    protected $table = 'feature_six_section';

    protected $fillable = [
        'page_title',
        'slug',
        'section_title',
        'section_description',
        'step_count',
        'title',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'step_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FeatureSixSection $section) {
            if ($section->isDirty('page_title')) {
                $section->slug = Str::slug($section->page_title);
            }
        });
    }
}