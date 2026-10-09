<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\RendersPrescriptionPdf;
use App\Http\Controllers\Controller;
use App\Models\CalculatedBolus;
use App\Models\CalculatedInfusion;
use App\Models\PrescriptionProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrescriptionPreviewController extends Controller
{
    use RendersPrescriptionPdf;

    public function __invoke(Request $request, PrescriptionProfile $profile): Response
    {
        $this->authorize('view', $profile);
        $data = $request->validate([
            'weight' => ['required', 'numeric', 'gt:0', 'max:1000'],
            'name' => ['nullable', 'string', 'max:191'],
            'patient_id' => ['nullable', 'string', 'max:191'],
        ]);
        $weight = (float) $data['weight'];
        $profile->load(['sections.items.bolus', 'sections.items.infusion.concentrations']);
        $profileSections = [];
        foreach ($profile->sections as $section) {
            $items = [];
            foreach ($section->items as $item) {
                if ($item->bolus !== null) {
                    $bolus = $item->bolus;
                    $items[] = ['type' => 'bolus', 'name' => $bolus->name,
                        'calculated' => ! $bolus->trashed() && $bolus->min_weight <= $weight && $bolus->max_weight > $weight
                            ? new CalculatedBolus($bolus, $weight) : null];
                } else {
                    $infusion = $item->infusion;
                    $preparation = $infusion?->concentrations->first(fn ($preparation): bool => $preparation->min_weight <= $weight && ($preparation->max_weight === null || $preparation->max_weight > $weight));
                    if ($preparation !== null) {
                        $preparation->setRelation('drug', $infusion);
                    }
                    $items[] = ['type' => 'infusion', 'name' => optional($infusion)->name ?? 'Recette indisponible',
                        'calculated' => $preparation !== null && ! $infusion->trashed() ? new CalculatedInfusion($preparation, $weight) : null];
                }
            }
            $profileSections[] = ['name' => $section->name, 'items' => $items];
        }

        return $this->renderPrescriptionPdf([
            'profileSections' => $profileSections, 'profileName' => $profile->name,
            'patient' => ['name' => $data['name'] ?? 'Patient fictif', 'id' => $data['patient_id'] ?? 'TEST',
                'weight' => $weight, 'dosingWeight' => $weight, 'isWeightEstimated' => 'false'],
        ]);
    }
}
