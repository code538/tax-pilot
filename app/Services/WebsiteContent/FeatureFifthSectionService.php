<?php

namespace App\Services\WebsiteContent;

use App\Models\FeatureFifthSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FeatureFifthSectionService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = FeatureFifthSection::query();

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

        return $query->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getBySlug(string $slug): Collection
    {
        return FeatureFifthSection::where('slug', $slug)
            ->orderBy('id')
            ->get();
    }

    public function find(int $id): FeatureFifthSection
    {
        return FeatureFifthSection::findOrFail($id);
    }

    public function create(array $data): FeatureFifthSection
    {
        return FeatureFifthSection::create($data);
    }

    public function update(
        FeatureFifthSection $section,
        array $data
    ): FeatureFifthSection {
        $section->update($data);

        return $section->refresh();
    }

    public function delete(FeatureFifthSection $section): bool
    {
        return (bool) $section->delete();
    }
}