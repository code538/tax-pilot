<?php

namespace App\Services\WebsiteContent;

use App\Models\FeatureSixSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FeatureSixSectionService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = FeatureSixSection::query();

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

        return $query->orderBy('step_count')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getActiveBySlug(string $slug): Collection
    {
        return FeatureSixSection::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('step_count')
            ->orderBy('id')
            ->get();
    }

    public function find(int $id): FeatureSixSection
    {
        return FeatureSixSection::findOrFail($id);
    }

    public function create(array $data): FeatureSixSection
    {
        return FeatureSixSection::create($data);
    }

    public function update(
        FeatureSixSection $section,
        array $data
    ): FeatureSixSection {
        $section->update($data);

        return $section->refresh();
    }

    public function delete(FeatureSixSection $section): bool
    {
        return (bool) $section->delete();
    }
}