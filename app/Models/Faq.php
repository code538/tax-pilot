<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faq extends Model
{
    protected $fillable = [
        'page_name',
        'slug',
        'question',
        'answer',
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
        static::saving(function (Faq $faq) {
            if ($faq->isDirty('page_name')) {
                $faq->slug = Str::slug($faq->page_name);
            }
        });
    }
}
