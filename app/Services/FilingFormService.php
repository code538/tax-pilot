<?php

namespace App\Services;

use App\Models\Filing;
use App\Models\FilingVersion;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FilingFormService
{
    public function getForm(
        int $organisationId,
        int $filingId
    ): FilingVersion {
        $filing = Filing::query()
            ->where('organisation_id', $organisationId)
            ->whereKey($filingId)
            ->firstOrFail();

        $filingVersion = FilingVersion::query()
            ->where('filing_id', $filing->id)
            ->orderByDesc('version_number')
            ->with([
                'filing',
                'formVersion.formDefinition',
                'formVersion.sections.fields',
            ])
            ->first();

        if (! $filingVersion) {
            throw (new ModelNotFoundException)
                ->setModel(FilingVersion::class);
        }

        return $filingVersion;
    }
}