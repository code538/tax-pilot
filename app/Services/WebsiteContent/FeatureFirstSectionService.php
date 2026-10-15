<?php

namespace App\Services\WebsiteContent;

use App\Models\FeatureFirstSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FeatureFirstSectionService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = FeatureFirstSection::query();

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
        return FeatureFirstSection::where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function find(int $id): FeatureFirstSection
    {
        return FeatureFirstSection::findOrFail($id);
    }

    public function create(array $data): FeatureFirstSection
    {
        return FeatureFirstSection::create($data);
    }

    public function update(
        FeatureFirstSection $section,
        array $data
    ): FeatureFirstSection {
        $section->update($data);

        return $section->refresh();
    }

    public function delete(FeatureFirstSection $section): bool
    {
        return (bool) $section->delete();
    }
}
