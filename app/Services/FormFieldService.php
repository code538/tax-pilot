<?php

namespace App\Services;

use App\Models\FormField;
use App\Models\FormSection;
use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FormFieldService
{
    public function all(
        int $formVersionId,
        int $formSectionId
    ): Collection {
        $section = $this->findSection($formVersionId, $formSectionId);

        return $section->fields()->get();
    }

    public function find(
        int $formVersionId,
        int $formSectionId,
        int $formFieldId
    ): FormField {
        return $this->findSection($formVersionId, $formSectionId)
            ->fields()
            ->findOrFail($formFieldId);
    }

    public function create(
        int $formVersionId,
        int $formSectionId,
        array $data
    ): FormField {
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

            $section = FormSection::query()
                ->whereKey($formSectionId)
                ->where('form_version_id', $version->id)
                ->firstOrFail();

            return $section->fields()->create($data);
        });
    }

    public function update(
        int $formVersionId,
        int $formSectionId,
        int $formFieldId,
        array $data
    ): FormField {
        return DB::transaction(function () use (
            $formVersionId,
            $formSectionId,
            $formFieldId,
            $data
        ) {
            $version = FormVersion::query()
                ->whereKey($formVersionId)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDraft($version);

            $section = FormSection::query()
                ->whereKey($formSectionId)
                ->where('form_version_id', $version->id)
                ->firstOrFail();

            $field = $section->fields()->findOrFail($formFieldId);
            $field->update($data);

            return $field->fresh();
        });
    }

    public function delete(
        int $formVersionId,
        int $formSectionId,
        int $formFieldId
    ): void {
        DB::transaction(function () use (
            $formVersionId,
            $formSectionId,
            $formFieldId
        ) {
            $version = FormVersion::query()
                ->whereKey($formVersionId)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDraft($version);

            $section = FormSection::query()
                ->whereKey($formSectionId)
                ->where('form_version_id', $version->id)
                ->firstOrFail();

            $section->fields()->findOrFail($formFieldId)->delete();
        });
    }

    private function findSection(
        int $formVersionId,
        int $formSectionId
    ): FormSection {
        return FormSection::query()
            ->whereKey($formSectionId)
            ->where('form_version_id', $formVersionId)
            ->with('fields')
            ->firstOrFail();
    }

    private function ensureDraft(FormVersion $version): void
    {
        if ($version->status !== 'draft') {
            throw ValidationException::withMessages([
                'form_version' => [
                    'Fields can only be changed while the form version is in draft status.',
                ],
            ]);
        }
    }
}