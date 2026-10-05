<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'form_section_id' => $this->form_section_id,
            'field_key' => $this->field_key,
            'label' => $this->label,
            'field_type' => $this->field_type,
            'placeholder' => $this->placeholder,
            'help_text' => $this->help_text,
            'is_required' => $this->is_required,
            'options' => $this->options,
            'validation_rules' => $this->validation_rules,
            'external_path' => $this->external_path,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}