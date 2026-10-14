<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LegalPage extends Model
{
    protected $fillable = [
        'page_name',
        'slug',
        'title',
        'description',
        'is_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (LegalPage $legalPage) {
            if ($legalPage->isDirty('page_name')) {
                $legalPage->slug = Str::slug(
                    $legalPage->page_name
                );
            }
        });
    }
}
