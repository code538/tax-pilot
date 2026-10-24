<?php

namespace App\Services\WebsiteContent;

use App\Models\FeatureForthSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FeatureForthSectionService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = FeatureForthSection::query();

        if (!empty($filters['page_name'])) {
            $query->where(
                'page_name',
                'like',
                '%' . $filters['page_name'] . '%'
            );
        }

        if (!empty($filters['slug'])) {
            $query->where('slug', $filters['slug']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getActiveBySlug(string $slug): Collection
    {
        return FeatureForthSection::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function find(int $id): FeatureForthSection
    {
        return FeatureForthSection::findOrFail($id);
    }

    public function create(array $data): FeatureForthSection
    {
        return FeatureForthSection::create($data);
    }

    public function update(
        FeatureForthSection $section,
        array $data
    ): FeatureForthSection {
        $section->update($data);

        return $section->refresh();
    }

    public function delete(FeatureForthSection $section): bool
    {
        return (bool) $section->delete();
    }
}