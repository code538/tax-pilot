<?php

namespace App\Services;


use App\Models\FilingType;
use App\Models\FormDefinition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormDefinitionService
{
    public function all(array $filters = []): Collection
    {
        return FormDefinition::query()
            ->with('filingType')
            ->when(
                isset($filters['filing_type_id']),
                fn ($query) => $query->where(
                    'filing_type_id',
                    $filters['filing_type_id']
                )
            )
            ->when(
                isset($filters['is_active']),
                fn ($query) => $query->where(
                    'is_active',
                    $filters['is_active']
                )
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): FormDefinition
    {
        return FormDefinition::query()
            ->with('filingType')
            ->findOrFail($id);
    }

    public function create(array $data): FormDefinition
    {
        $this->ensureFilingTypeExists((int) $data['filing_type_id']);

        $form = FormDefinition::create($data);

        return $form->load('filingType');
    }

    public function update(
        int $id,
        array $data
    ): FormDefinition {
        $form = FormDefinition::query()->findOrFail($id);

        if (isset($data['filing_type_id'])) {
            $this->ensureFilingTypeExists(
                (int) $data['filing_type_id']
            );
        }

        $form->update($data);

        return $form->fresh()->load('filingType');
    }

    public function deactivate(int $id): FormDefinition
    {
        $form = FormDefinition::query()->findOrFail($id);

        $form->update(['is_active' => false]);

        return $form->fresh()->load('filingType');
    }

    private function ensureFilingTypeExists(int $filingTypeId): void
    {
        $exists = FilingType::query()
            ->whereKey($filingTypeId)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'filing_type_id' => [
                    'The selected filing type does not exist.',
                ],
            ]);
        }
    }
}