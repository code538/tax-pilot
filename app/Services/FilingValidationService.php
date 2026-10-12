<?php

namespace App\Services;

use App\Models\Filing;
use App\Models\FilingFieldValue;
use App\Models\FilingVersion;
use App\Models\FormField;
use App\Models\ValidationResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FilingValidationService
{
    public function validateFiling(
        int $organisationId,
        int $filingId,
        int $userId
    ): ValidationResult {
        // Ensure the filing belongs to this organisation.
        $filing = Filing::query()
            ->where('organisation_id', $organisationId)
            ->whereKey($filingId)
            ->firstOrFail();

        // Validate the latest filing version.
        $version = FilingVersion::query()
            ->where('filing_id', $filing->id)
            ->with([
                'formVersion.sections.fields' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('sort_order');
                },
            ])
            ->orderByDesc('version_number')
            ->firstOrFail();

        if (! $version->formVersion) {
            abort(422, 'No form version is attached to this filing version.');
        }

        // Load saved answers for this exact version.
        $savedValues = FilingFieldValue::query()
            ->where('filing_id', $filing->id)
            ->where('filing_version_id', $version->id)
            ->get()
            ->keyBy('form_field_id');

        $errors = [];

        // Validate every active field in the pinned form.
        foreach ($version->formVersion->sections as $section) {
            foreach ($section->fields as $field) {
                $savedValue = $savedValues->get($field->id);
                $value = $savedValue?->value;

                $rules = $this->rulesFor($field);

                $validator = Validator::make(
                    ['value' => $value],
                    ['value' => $rules]
                );

                if ($validator->fails()) {
                    $errors[] = [
                        'form_field_id' => $field->id,
                        'field_key' => $field->field_key,
                        'label' => $field->label,
                        'section' => $section->name,
                        'messages' => $validator
                            ->errors()
                            ->get('value'),
                    ];
                }
            }
        }

        $status = empty($errors) ? 'passed' : 'failed';

        // Keep each validation attempt for audit/history.
        return DB::transaction(function () use (
            $filing,
            $version,
            $userId,
            $errors,
            $status
        ) {
            return ValidationResult::create([
                'filing_id' => $filing->id,
                'filing_version_id' => $version->id,
                'status' => $status,
                'error_count' => count($errors),
                'warning_count' => 0,
                'errors' => $errors,
                'warnings' => [],
                'validated_by' => $userId,
                'validated_at' => now(),
            ]);
        });
    }

    /**
     * Build Laravel validation rules from a dynamic form field.
     */
    private function rulesFor(FormField $field): array
    {
        $rules = $field->is_required
            ? ['required']
            : ['nullable'];

        switch ($field->field_type) {
            case 'text':
            case 'textarea':
            case 'file':
                $rules[] = 'string';
                break;

            case 'number':
            case 'currency':
                $rules[] = 'numeric';
                break;

            case 'date':
                $rules[] = 'date';
                break;

            case 'email':
                $rules[] = 'email';
                break;

            case 'boolean':
            case 'checkbox':
                $rules[] = 'boolean';
                break;

            case 'select':
            case 'radio':
                $options = $field->options ?? [];

                $allowedValues = collect($options)
                    ->map(function ($option) {
                        if (is_array($option)) {
                            return $option['value'] ?? null;
                        }

                        return $option;
                    })
                    ->filter(fn ($value) => $value !== null)
                    ->values()
                    ->all();

                if (! empty($allowedValues)) {
                    $rules[] = Rule::in($allowedValues);
                }

                break;
        }

        // Add additional rules configured by the admin.
        $customRules = $field->validation_rules ?? [];

        if (is_array($customRules)) {
            $rules = array_merge($rules, $customRules);
        }

        return $rules;
    }
}