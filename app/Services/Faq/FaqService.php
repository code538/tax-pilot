<?php

namespace App\Services\Faq;

use App\Models\Faq;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FaqService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Faq::query();

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

    public function getActiveFaqs(): Collection
    {
        return Faq::where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function getActiveFaqsBySlug(string $slug): Collection
    {
        return Faq::where('slug', $slug)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();
    }

    public function find(int $id): Faq
    {
        return Faq::findOrFail($id);
    }

    public function create(array $data): Faq
    {
        return Faq::create($data);
    }

    public function update(Faq $faq, array $data): Faq
    {
        $faq->update($data);

        return $faq->refresh();
    }

    public function delete(Faq $faq): bool
    {
        return (bool) $faq->delete();
    }
}
