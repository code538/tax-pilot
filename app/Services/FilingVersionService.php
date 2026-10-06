<?php

namespace App\Services;

use App\Models\Filing;
use App\Models\FilingVersion;
use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FilingVersionService
{
    public function all(int $filingId): Collection
    {
        return FilingVersion::query()
            ->where('filing_id', $filingId)
            ->with([
                'filing',
                'formVersion.formDefinition',
                'preparedBy',
                'supersedesVersion',
            ])
            ->orderByDesc('version_number')
            ->get();
    }

    public function find(
        int $filingId,
        int $filingVersionId
    ): FilingVersion {
        return FilingVersion::query()
            ->where('filing_id', $filingId)
            ->with([
                'filing',
                'formVersion.formDefinition',
                'preparedBy',
                'supersedesVersion',
            ])
            ->findOrFail($filingVersionId);
    }

    /**
     * Create the first filing version.
     *
     * The correct published form version is selected automatically
     * from the filing's filing type.
     */
    public function create(
        int $filingId,
        int $userId,
        ?string $revisionReason = null
    ): FilingVersion {
        return DB::transaction(function () use (
            $filingId,
            $userId,
            $revisionReason
        ) {
            $filing = Filing::query()
                ->with('filingType')
                ->whereKey($filingId)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Find the latest published form version for
             * this filing's filing type.
             */
            $formVersion = $this->getPublishedFormVersion(
                $filing->filing_type_id
            );

            $existingVersion = FilingVersion::query()
                ->where('filing_id', $filing->id)
                ->orderByDesc('version_number')
                ->lockForUpdate()
                ->first();

            $versionNumber = ($existingVersion?->version_number ?? 0) + 1;

            $filingVersion = FilingVersion::query()->create([
                'filing_id' => $filing->id,
                'organisation_id' => $filing->organisation_id,
                'form_version_id' => $formVersion->id,
                'version_number' => $versionNumber,
                'supersedes_version_id' => $existingVersion?->id,
                'revision_reason' => $revisionReason,
                'prepared_by' => $userId,
                'prepared_at' => now(),
            ]);

            return $filingVersion->fresh()->load([
                'filing',
                'formVersion.formDefinition',
                'preparedBy',
                'supersedesVersion',
            ]);
        });
    }

    /**
     * Get the currently published form version
     * for a particular filing type.
     */
    private function getPublishedFormVersion(
        int $filingTypeId
    ): FormVersion {
        $formVersion = FormVersion::query()
            ->where('status', 'published')
            ->whereHas('formDefinition', function ($query) use ($filingTypeId) {
                $query->where('filing_type_id', $filingTypeId)
                    ->where('is_active', true);
            })
            ->with('formDefinition')
            ->orderByDesc('published_at')
            ->orderByDesc('version_number')
            ->first();

        if (! $formVersion) {
            throw ValidationException::withMessages([
                'form_version' => [
                    'No published form version is available for this filing type.',
                ],
            ]);
        }

        return $formVersion;
    }
}