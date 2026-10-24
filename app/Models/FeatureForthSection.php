<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeatureForthSection extends Model
{
    protected $table = 'feature_forth_section';

    protected $fillable = [
        'page_name',
        'slug',
        'title',
        'description',
        'feature',
        'mobile_image',
        'web_image',
        'image_alt',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'feature' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FeatureForthSection $section) {
            if ($section->isDirty('page_name')) {
                $section->slug = Str::slug($section->page_name);
            }
        });
    }
}