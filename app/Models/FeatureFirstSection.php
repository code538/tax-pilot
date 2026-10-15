<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeatureFirstSection extends Model
{
    protected $table = 'feature_first_section';

    protected $fillable = [
        'page_title',
        'slug',
        'batch',
        'heading',
        'heading_meta',
        'description',
        'desc_meta',
        'primary_button',
        'primary_button_url',
        'secondary_button',
        'secondary_button_url',
        'trust_text',
        'web_image',
        'mobile_image',
        'image_alt',
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
        static::saving(function (FeatureFirstSection $section) {
            if ($section->isDirty('page_title')) {
                $section->slug = Str::slug($section->page_title);
            }
        });
    }
}
