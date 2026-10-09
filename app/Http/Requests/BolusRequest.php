<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BolusRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'instructions' => $this->input('instructions') ?? '',
        ]);
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'brand_name' => ['nullable', 'string', 'max:191'],
            'unit' => ['required', 'string', 'max:32'],
            'commercial_concentration' => ['required', 'numeric', 'min:0'],
            'dosage' => ['required', 'numeric', 'min:0'],
            'minimum_dose' => ['required', 'numeric', 'min:0'],
            'maximum_dose' => ['required', 'numeric', 'gte:minimum_dose'],
            'dose_precision' => ['required', 'integer', 'between:0,6'],
            'volume_precision' => ['required', 'integer', 'between:0,6'],
            'type' => ['required', 'integer', 'in:1,2,3'],
            'min_weight' => ['required', 'numeric', 'min:0'],
            'max_weight' => ['required', 'numeric', 'gt:min_weight'],
            'instructions' => ['nullable', 'string', 'max:191'],
            'asterisk' => ['sometimes', 'boolean'],
        ];
    }
}
