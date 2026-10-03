<?php

namespace App\Services;

use App\Models\FormDefinition;
use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormVersionService
{
    public function all(): Collection
    {
        return FormVersion::query()
            ->with(['formDefinition', 'createdBy'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function find(int $id): FormVersion
    {
        return FormVersion::query()
            ->with(['formDefinition', 'createdBy'])
            ->findOrFail($id);
    }

    public function create(int $formDefinitionId, int $userId): FormVersion
    {
        return DB::transaction(function () use (
            $formDefinitionId,
            $userId
        ) {
            $definition = FormDefinition::query()
                ->whereKey($formDefinitionId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $definition->is_active) {
                throw ValidationException::withMessages([
                    'form_definition_id' => [
                        'Cannot create a version for an inactive form.',
                    ],
                ]);
            }

            $latest = $definition->versions()
                ->orderByDesc('version_number')
                ->first();

            $version = $definition->versions()->create([
                'version_number' => ($latest?->version_number ?? 0) + 1,
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            return $version->load(['formDefinition', 'createdBy']);
        });
    }

    public function publish(int $id): FormVersion
    {
        return DB::transaction(function () use ($id) {
            $version = FormVersion::query()
                ->whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($version->status !== 'draft') {
                throw ValidationException::withMessages([
                    'status' => [
                        'Only draft versions can be published.',
                    ],
                ]);
            }

            $version->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            return $version->fresh()->load([
                'formDefinition',
                'createdBy',
            ]);
        });
    }

    public function deleteDraft(int $id): void
    {
        $version = FormVersion::query()->findOrFail($id);

        if ($version->status !== 'draft') {
            throw ValidationException::withMessages([
                'form_version' => [
                    'Published versions cannot be deleted.',
                ],
            ]);
        }

        $version->delete();
    }
}