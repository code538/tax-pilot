<?php

namespace Database\Seeders;

use App\Models\FilingType;
use Illuminate\Database\Seeder;

class FilingTypeSeeder extends Seeder
{
    public function run(): void
    {
       $types = [
        [
            'name' => 'CT600',
            'slug' => 'ct600',
            'category' => 'tax',
            'authority' => 'hmrc',
            'description' => 'Corporation Tax filing submitted to HMRC.',
            'is_active' => true,
            'sort_order' => 10,
        ],

        [
            'name' => 'Companies House',
            'slug' => 'companies-house',
            'category' => 'companies_house',
            'authority' => 'companies_house',
            'description' => 'Companies House filing and submission.',
            'is_active' => true,
            'sort_order' => 20,
        ],
    ];

        foreach ($types as $type) {
            FilingType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}