<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeatureThirdSection extends Model
{
    protected $table = 'feature_third_section';

    protected $fillable = [
        'page_title',
        'slug',
        'section_heading',
        'section_desc',
        'icon',
        'title',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FeatureThirdSection $section) {
            if ($section->isDirty('page_title')) {
                $section->slug = Str::slug($section->page_title);
            }
        });
    }
}