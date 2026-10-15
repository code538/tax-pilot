<?php

namespace App\Services\WebsiteContent;

use App\Models\FeatureSecondSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FeatureSecondSectionService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = FeatureSecondSection::query();

        if (!empty($filters['page_title'])) {
            $query->where('page_title', $filters['page_title']);
        }

        if (!empty($filters['slug'])) {
            $query->where('slug', $filters['slug']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate(
            (int) ($filters['per_page'] ?? 15)
        );
    }

    public function getActiveBySlug(string $slug): Collection
    {
        return FeatureSecondSection::where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function find(int $id): FeatureSecondSection
    {
        return FeatureSecondSection::findOrFail($id);
    }

    public function create(array $data): FeatureSecondSection
    {
        return FeatureSecondSection::create($data);
    }

    public function update(
        FeatureSecondSection $section,
        array $data
    ): FeatureSecondSection {
        $section->update($data);

        return $section->refresh();
    }

    public function delete(FeatureSecondSection $section): bool
    {
        return (bool) $section->delete();
    }
}
