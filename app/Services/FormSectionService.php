<?php

namespace App\Services;

use App\Models\FormSection;
use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormSectionService
{
    public function all(int $formVersionId): Collection
    {
        $version = FormVersion::query()->findOrFail($formVersionId);

        return $version->sections()->get();
    }

    public function find(
        int $formVersionId,
        int $formSectionId
    ): FormSection {
        return FormSection::query()
            ->where('form_version_id', $formVersionId)
            ->findOrFail($formSectionId);
    }

    public function create(
        int $formVersionId,
        array $data
    ): FormSection {
        return DB::transaction(function () use (
            $formVersionId,
            $data
        ) {
            $version = FormVersion::query()
                ->whereKey($formVersionId)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDraft($version);

            $section = $version->sections()->create($data);

            return $section->refresh();
        });
    }

    public function update(
        int $formVersionId,
        int $formSectionId,
        array $data
    ): FormSection {
        return DB::transaction(function () use (
            $formVersionId,
            $formSectionId,
            $data
        ) {
            $version = FormVersion::query()
                ->whereKey($formVersionId)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDraft($version);

            $section = $version->sections()
                ->whereKey($formSectionId)
                ->firstOrFail();

            $section->update($data);

            return $section->fresh();
        });
    }

    public function delete(
        int $formVersionId,
        int $formSectionId
    ): void {
        DB::transaction(function () use (
            $formVersionId,
            $formSectionId
        ) {
            $version = FormVersion::query()
                ->whereKey($formVersionId)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDraft($version);

            $section = $version->sections()
                ->whereKey($formSectionId)
                ->firstOrFail();

            $section->delete();
        });
    }

    private function ensureDraft(FormVersion $version): void
    {
        if ($version->status !== 'draft') {
            throw ValidationException::withMessages([
                'form_version' => [
                    'Sections can only be changed while the form version is in draft status.',
                ],
            ]);
        }
    }
}