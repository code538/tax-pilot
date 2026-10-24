<?php

namespace App\Services\WebsiteContent;

use App\Models\FeatureSeventhSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FeatureSeventhSectionService
{
    public function getAll(
        array $filters = []
    ): LengthAwarePaginator {
        $query = FeatureSeventhSection::query();

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

        if (array_key_exists('is_active', $filters)) {
            $query->where(
                'is_active',
                filter_var(
                    $filters['is_active'],
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        return $query
            ->latest('id')
            ->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function getActiveBySlug(
        string $slug
    ): Collection {
        return FeatureSeventhSection::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function find(
        int $id
    ): FeatureSeventhSection {
        return FeatureSeventhSection::findOrFail($id);
    }

    public function create(
        array $data
    ): FeatureSeventhSection {
        return FeatureSeventhSection::create($data);
    }

    public function update(
        FeatureSeventhSection $section,
        array $data
    ): FeatureSeventhSection {
        $section->update($data);

        return $section->refresh();
    }

    public function delete(
        FeatureSeventhSection $section
    ): bool {
        return (bool) $section->delete();
    }
}
