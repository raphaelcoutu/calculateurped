<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfusionDrugRequest extends FormRequest
{
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
            'concentration' => ['required', 'string', 'max:191'],
            'debit_min' => ['required', 'numeric', 'min:0'],
            'debit_max' => ['required', 'numeric', 'min:0'],
            'debit_dose_unit' => ['required', 'in:mg,mcg,unité,mU'],
            'debit_time_unit' => ['required', 'in:min,h'],
            'debit_min_limit' => ['required', 'numeric', 'min:0'],
            'debit_max_limit' => ['required', 'numeric', 'min:0'],
            'debit_limit_unit' => ['required', 'in:mg,mcg,unité,mU'],
            'dosage_precision' => ['required', 'integer', 'between:0,6'],
            'type' => ['required', 'integer', 'in:1,2,3'],
            'order' => ['required', 'integer', 'between:0,32767'],
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
