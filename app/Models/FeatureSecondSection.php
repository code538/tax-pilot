<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeatureSecondSection extends Model
{
    protected $table = 'feature_second_section';

    protected $fillable = [
        'page_title',
        'slug',
        'title',
        'heading',
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
        static::saving(function (FeatureSecondSection $section) {
            if ($section->isDirty('page_title')) {
                $section->slug = Str::slug($section->page_title);
            }
        });
    }
}
