<?php

namespace App\Http\Requests;

use App\Models\PrescriptionProfile;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PrescriptionProfileRequest extends FormRequest
{
    public function authorize(): bool|Response
    {
        $profile = $this->route('profile');

        return $profile === null
            ? ($this->user()?->can('create', PrescriptionProfile::class) ?? false)
            : Gate::inspect('update', $profile);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'sections' => ['required', 'array', 'list', 'min:1', 'max:100'],
            'sections.*' => ['required', 'array:name,items'],
            'sections.*.name' => ['required', 'string', 'max:191'],
            'sections.*.items' => ['present', 'array', 'list', 'max:100'],
            'sections.*.items.*' => ['required', 'array:type,recipe_id'],
            'sections.*.items.*.type' => ['required', Rule::in(['bolus', 'infusion'])],
            'sections.*.items.*.recipe_id' => ['required', 'integer', 'min:1'],
        ];
    }
}
