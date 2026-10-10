<?php

namespace App\Services;

use App\Models\Filing;
use App\Models\FilingFieldValue;
use App\Models\FilingVersion;
use App\Models\FormField;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FilingFieldValueService
{
    /**
     * Get all saved values for the current filing version.
     */
    public function all(
        int $organisationId,
        int $filingId,
        int $filingVersionId
    ) {
        $filing = $this->getFiling(
            $organisationId,
            $filingId
        );

        $version = FilingVersion::query()
            ->where('id', $filingVersionId)
            ->where('filing_id', $filing->id)
            ->firstOrFail();

        return FilingFieldValue::query()
            ->where('filing_id', $filing->id)
            ->where('filing_version_id', $version->id)
            ->with('formField')
            ->get();
    }

    /**
     * Save multiple field values.
     */
    public function save(
        int $organisationId,
        int $filingId,
        int $filingVersionId,
        array $values
    ) {
        $filing = $this->getFiling(
            $organisationId,
            $filingId
        );

        $version = FilingVersion::query()
            ->where('id', $filingVersionId)
            ->where('filing_id', $filing->id)
            ->with('formVersion')
            ->firstOrFail();

        /*
         * Do not allow editing a locked version.
         */
        if ($version->locked_at) {
            throw ValidationException::withMessages([
                'filing_version' => [
                    'This filing version is locked and cannot be edited.'
                ],
            ]);
        }

        return DB::transaction(function () use (
            $filing,
            $version,
            $values
        ) {
            $saved = [];

            foreach ($values as $item) {

                $field = FormField::query()
                    ->whereKey($item['form_field_id'])
                    ->where('is_active', true)
                    ->whereHas('section', function ($query) use ($version) {
                        $query->where(
                            'form_version_id',
                            $version->form_version_id
                        );
                    })
                    ->first();

                if (!$field) {
                    throw ValidationException::withMessages([
                        'form_field_id' => [
                            'The selected form field does not belong to this filing version.'
                        ],
                    ]);
                }

                /*
                 * Basic required-field validation.
                 */
                if (
                    $field->is_required &&
                    (
                        !array_key_exists('value', $item) ||
                        $item['value'] === null ||
                        $item['value'] === ''
                    )
                ) {
                    throw ValidationException::withMessages([
                        "values.{$field->id}.value" => [
                            "{$field->label} is required."
                        ],
                    ]);
                }

                $saved[] = FilingFieldValue::updateOrCreate(
                    [
                        'filing_version_id' => $version->id,
                        'form_field_id' => $field->id,
                    ],
                    [
                        'filing_id' => $filing->id,
                        'value' => $item['value'] ?? null,
                    ]
                );
            }

            return collect($saved)
                ->load('formField');
        });
    }

    /**
     * Get filing belonging to organisation.
     */
    private function getFiling(
        int $organisationId,
        int $filingId
    ): Filing {
        return Filing::query()
            ->where('organisation_id', $organisationId)
            ->whereKey($filingId)
            ->firstOrFail();
    }
}