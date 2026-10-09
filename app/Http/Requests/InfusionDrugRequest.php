<?php

namespace App\Http\Requests;

use App\Models\InfusionDrug;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class InfusionDrugRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $unit = $this->input('dose_unit');
        if (is_string($unit) && in_array($unit, InfusionDrug::doseUnits(), true)) {
            $parts = explode('/', $unit);
            $this->merge([
                'debit_dose_unit' => $parts[0],
                'debit_time_unit' => $parts[count($parts) - 1],
                'dose_per_kg' => count($parts) === 3,
            ]);
        }
    }

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, list<string|ValidationRule|In>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'brand_name' => ['nullable', 'string', 'max:191'],
            'concentration' => ['required', 'string', 'max:191'],
            'debit_min' => ['required', 'numeric', 'min:0'],
            'debit_max' => ['required', 'numeric', 'min:0'],
            'dose_unit' => ['required', Rule::in(InfusionDrug::doseUnits())],
            'dose_per_kg' => ['required', 'boolean'],
            'debit_dose_unit' => ['required', 'in:mg,mcg,unité,mU'],
            'debit_time_unit' => ['required', 'in:min,h'],
            'debit_min_limit' => ['required', 'numeric', 'min:0'],
            'debit_max_limit' => ['required', 'numeric', 'min:0'],
            'debit_limit_unit' => ['required', 'in:mg,mcg,unité,mU'],
            'dosage_precision' => ['required', 'integer', 'between:0,6'],
            'preparations' => ['required', 'array', 'list', 'min:1', 'max:100'],
            'preparations.*' => ['required', 'array:min_weight,max_weight,concentration,concentration_unit,total_volume,instructions'],
            'preparations.*.min_weight' => ['required', 'numeric', 'min:0'],
            'preparations.*.max_weight' => ['nullable', 'numeric', 'gt:preparations.*.min_weight'],
            'preparations.*.concentration' => ['required', 'numeric', 'gt:0'],
            'preparations.*.concentration_unit' => ['required', 'in:mg,mcg,unité,mU'],
            'preparations.*.total_volume' => ['required', 'numeric', 'gt:0'],
            'preparations.*.instructions' => ['nullable', 'string', 'max:191'],
        ];
    }
}
