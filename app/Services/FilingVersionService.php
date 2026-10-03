<?php

namespace App\Services;

use App\Models\Filing;
use App\Models\FilingVersion;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FilingVersionService
{
    public function all(
        Organisation $organisation,
        Filing $filing
    ): Collection {
        $this->ensureFilingBelongsToOrganisation(
            $organisation,
            $filing
        );

        return $filing->versions()
            ->with('preparedBy')
            ->orderByDesc('version_number')
            ->get();
    }

    public function find(
        Organisation $organisation,
        Filing $filing,
        int $versionId
    ): FilingVersion {
        $this->ensureFilingBelongsToOrganisation(
            $organisation,
            $filing
        );

        return $filing->versions()
            ->with([
                'preparedBy',
                'supersedes',
            ])
            ->where('id', $versionId)
            ->firstOrFail();
    }

    public function create(
        Organisation $organisation,
        Filing $filing,
        array $data,
        int $userId
    ): FilingVersion {
        $this->ensureFilingBelongsToOrganisation(
            $organisation,
            $filing
        );

        return DB::transaction(function () use (
            $organisation,
            $filing,
            $data,
            $userId
        ) {
            $latestVersion = $filing->versions()
                ->orderByDesc('version_number')
                ->first();

            $nextVersion = $latestVersion
                ? $latestVersion->version_number + 1
                : 1;

            $version = FilingVersion::create([
                'filing_id' => $filing->id,
                'organisation_id' => $organisation->id,
                'version_number' => $nextVersion,
                'supersedes_version_id' => $latestVersion?->id,
                'revision_reason' => $data['revision_reason'] ?? null,
                'prepared_by' => $userId,
                'prepared_at' => now(),
            ]);

            return $version->load([
                'preparedBy',
                'supersedes',
            ]);
        });
    }

    private function ensureFilingBelongsToOrganisation(
        Organisation $organisation,
        Filing $filing
    ): void {
        if ($filing->organisation_id !== $organisation->id) {
            abort(404, 'Filing not found.');
        }
    }
}