<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeatureFifthSection extends Model
{
    protected $table = 'feature_fifth_section';

    protected $fillable = [
        'page_name',
        'slug',
        'title',
        'description',
        'feature',
    ];

    protected function casts(): array
    {
        return [
            'feature' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FeatureFifthSection $section) {
            if ($section->isDirty('page_name')) {
                $section->slug = Str::slug($section->page_name);
            }
        });
    }
}