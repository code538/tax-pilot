<?php

namespace App\Services\LegalPage;

use App\Models\LegalPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class LegalPageService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = LegalPage::query();

        if (!empty($filters['page_name'])) {
            $query->where('page_name', $filters['page_name']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate(
            (int) ($filters['per_page'] ?? 15)
        );
    }

    public function getActivePages(): \Illuminate\Database\Eloquent\Collection
    {
        return LegalPage::where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function getActivePageBySlug(string $slug): Collection
    {
        return LegalPage::where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('is_order')
            ->get();
    }

    public function find(int $id): LegalPage
    {
        return LegalPage::findOrFail($id);
    }

    public function create(array $data): LegalPage
    {
        return LegalPage::create($data);
    }

    public function update(LegalPage $legalPage, array $data): LegalPage
    {
        $legalPage->update($data);

        return $legalPage->refresh();
    }

    public function delete(LegalPage $legalPage): bool
    {
        return (bool) $legalPage->delete();
    }
}
