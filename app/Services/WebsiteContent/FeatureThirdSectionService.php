<?php

namespace App\Services\WebsiteContent;

use App\Models\FeatureThirdSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FeatureThirdSectionService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = FeatureThirdSection::query();

        if (!empty($filters['page_title'])) {
            $query->where(
                'page_title',
                'like',
                '%' . $filters['page_title'] . '%'
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
        return FeatureThirdSection::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function find(int $id): FeatureThirdSection
    {
        return FeatureThirdSection::findOrFail($id);
    }

    public function create(array $data): FeatureThirdSection
    {
        return FeatureThirdSection::create($data);
    }

    public function update(
        FeatureThirdSection $section,
        array $data
    ): FeatureThirdSection {
        $section->update($data);

        return $section->refresh();
    }

    public function delete(FeatureThirdSection $section): bool
    {
        return (bool) $section->delete();
    }
}