<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilingFormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $formVersion = $this->formVersion;

        return [
            'filing' => [
                'id' => $this->filing_id,
                'reference' => $this->filing->reference,
                'status' => $this->filing->status,
            ],

            'form' => [
                'id' => $formVersion->formDefinition->id,
                'name' => $formVersion->formDefinition->name,
                'slug' => $formVersion->formDefinition->slug,
                'version_id' => $formVersion->id,
                'version_number' => $formVersion->version_number,
            ],

            'sections' => $formVersion->sections
                ->map(function ($section) {
                    return [
                        'id' => $section->id,
                        'name' => $section->name,
                        'slug' => $section->slug,
                        'description' => $section->description,
                        'sort_order' => $section->sort_order,

                        'fields' => $section->fields
                            ->where('is_active', true)
                            ->map(function ($field) {
                                return [
                                    'id' => $field->id,
                                    'field_key' => $field->field_key,
                                    'label' => $field->label,
                                    'field_type' => $field->field_type,
                                    'placeholder' => $field->placeholder,
                                    'help_text' => $field->help_text,
                                    'is_required' => $field->is_required,
                                    'options' => $field->options,
                                    'validation_rules' => $field->validation_rules,
                                    'sort_order' => $field->sort_order,

                                    // Answer will be added later.
                                    'value' => null,
                                ];
                            })
                            ->values(),
                    ];
                })
                ->values(),
        ];
    }
}