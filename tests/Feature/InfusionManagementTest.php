<?php

use App\Models\InfusionDrug;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('enregistre une fiche et ses préparations uniquement dans le centre de l’administrateur', function (): void {
    $organization = Organization::factory()->create();
    $administrator = User::factory()->for($organization)->create();
    $otherAdministrator = User::factory()->for(Organization::factory())->create();

    $this->actingAs($administrator)->post('/admin/infusions', infusionInput([
        'organization_id' => $otherAdministrator->organization_id,
        'status' => 'published',
    ]))->assertRedirect();

    $drug = InfusionDrug::where('organization_id', $organization->id)->firstOrFail();
    expect($drug->status)->toBe('draft');
    expect($drug->concentrations)->toHaveCount(2);
    $this->get("/admin/infusions/{$drug->id}")
        ->assertInertia(fn ($page) => $page->component('admin/infusions/show')
            ->where('infusion.name', 'Adrénaline')
            ->has('infusion.preparations', 2));
    $this->actingAs($otherAdministrator)->get("/admin/infusions/{$drug->id}")->assertNotFound();
    $this->put("/admin/infusions/{$drug->id}", infusionInput(['name' => 'Interdit']))->assertNotFound();
    expect($drug->fresh()->name)->toBe('Adrénaline');
});

it('traite un poids maximal de zéro comme une plage sans limite supérieure', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $input = infusionInput();
    $input['preparations'][1]['max_weight'] = 0;

    $this->actingAs($administrator)->post('/admin/infusions', $input)->assertRedirect();

    $drug = InfusionDrug::firstOrFail();
    $this->assertDatabaseHas('infusion_concentrations', [
        'infusion_drug_id' => $drug->id,
        'min_weight' => 10,
        'max_weight' => null,
    ]);
});

/** @return array<string, mixed> */
function infusionInput(array $overrides = []): array
{
    return array_replace([
        'name' => 'Adrénaline', 'brand_name' => '', 'concentration' => '1 mg/mL',
        'debit_min' => 0.1, 'debit_max' => 1, 'dose_unit' => 'mcg/kg/min',
        'debit_min_limit' => 0, 'debit_max_limit' => 0,
        'debit_limit_unit' => 'mcg', 'dosage_precision' => 2,
        'preparations' => [
            ['min_weight' => 0, 'max_weight' => 10, 'concentration' => 10, 'concentration_unit' => 'mcg', 'total_volume' => 50, 'instructions' => 'Première préparation'],
            ['min_weight' => 10, 'max_weight' => null, 'concentration' => 20, 'concentration_unit' => 'mcg', 'total_volume' => 100, 'instructions' => 'Seconde préparation'],
        ],
    ], $overrides);
}

it('copie toutes les préparations publiées dans un groupe indépendant', function (): void {
    $owner = User::factory()->for(Organization::factory())->create();
    $recipient = User::factory()->for(Organization::factory())->create();
    $this->actingAs($owner)->post('/admin/infusions', infusionInput())->assertRedirect();
    $source = InfusionDrug::firstOrFail();
    $source->update(['status' => 'published', 'published_at' => now()]);

    $this->actingAs($recipient)->post("/admin/infusions/{$source->id}/copy")->assertRedirect();

    $copy = InfusionDrug::where('organization_id', $recipient->organization_id)->firstOrFail();
    expect($copy->recipe_id)->not->toBe($source->recipe_id);
    expect($copy->status)->toBe('draft');
    expect($copy->recipeSnapshot())->toEqual($source->recipeSnapshot());
    expect($copy->concentrations->pluck('id')->intersect($source->concentrations->pluck('id')))->toBeEmpty();
    $source->concentrations()->update(['instructions' => 'Source modifiée']);
    expect($copy->fresh()->concentrations->first()->instructions)->toBe('Première préparation');
});

it('conserve le groupe publié pendant la révision et historise la nouvelle publication', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $this->actingAs($administrator)->post('/admin/infusions', infusionInput())->assertRedirect();
    $source = InfusionDrug::firstOrFail();
    $this->post("/admin/infusions/{$source->id}/publish")->assertRedirect();
    $this->post("/admin/infusions/{$source->id}/revise")->assertRedirect();
    $draft = InfusionDrug::where('status', 'draft')->firstOrFail();
    $this->post("/admin/infusions/{$source->id}/revise")->assertConflict();
    expect($draft->recipeSnapshot())->toEqual($source->fresh()->recipeSnapshot());
    $input = infusionInput(['name' => 'Adrénaline révisée']);
    $input['preparations'] = array_reverse($input['preparations']);
    $input['preparations'][0]['concentration'] = 30;

    $this->put("/admin/infusions/{$draft->id}", $input)->assertRedirect();

    expect(InfusionDrug::published()->pluck('id')->all())->toBe([$source->id]);
    expect($source->fresh()->name)->toBe('Adrénaline');
    expect($source->fresh()->concentrations->first()->concentration)->toBe(10.0);
    expect($draft->fresh()->concentrations->first()->concentration)->toBe(30.0);
    $this->post("/admin/infusions/{$draft->id}/publish")->assertRedirect();
    expect(InfusionDrug::published()->pluck('id')->all())->toBe([$draft->id]);
    expect($draft->fresh()->published_by)->toBe($administrator->id);
    expect($draft->fresh()->published_at)->not->toBeNull();
    $this->assertDatabaseHas('infusion_activity', ['infusion_drug_id' => $draft->id, 'action' => 'updated', 'user_id' => $administrator->id]);
    $this->assertDatabaseHas('infusion_activity', ['infusion_drug_id' => $draft->id, 'action' => 'published', 'user_id' => $administrator->id]);
    $this->put("/admin/infusions/{$draft->id}", infusionInput())->assertNotFound();
});

it('supprime et restaure une fiche complète sans perdre ses préparations ni son historique', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $this->actingAs($administrator)->post('/admin/infusions', infusionInput())->assertRedirect();
    $drug = InfusionDrug::firstOrFail();
    $this->post("/admin/infusions/{$drug->id}/publish")->assertRedirect();

    $this->delete("/admin/infusions/{$drug->id}")->assertRedirect();

    $this->assertSoftDeleted($drug);
    expect(InfusionDrug::published()->count())->toBe(0);
    expect($drug->concentrations()->count())->toBe(2);
    $this->post("/admin/infusions/{$drug->id}/restore")->assertRedirect();
    expect(InfusionDrug::published()->pluck('id')->all())->toBe([$drug->id]);
    $this->assertDatabaseHas('infusion_activity', ['infusion_drug_id' => $drug->id, 'action' => 'deleted']);
    $this->assertDatabaseHas('infusion_activity', ['infusion_drug_id' => $drug->id, 'action' => 'restored']);
});

it('avertit des trous entre zéro et cent kilos sans empêcher la publication', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $input = infusionInput();
    $input['preparations'][0]['min_weight'] = 5;
    $input['preparations'][1]['min_weight'] = 20;
    $input['preparations'][1]['max_weight'] = 80;
    $this->actingAs($administrator)->post('/admin/infusions', $input)->assertRedirect();
    $drug = InfusionDrug::firstOrFail();

    $this->get("/admin/infusions/{$drug->id}")->assertInertia(fn ($page) => $page
        ->where('gaps', [['min' => 0, 'max' => 5], ['min' => 10, 'max' => 20], ['min' => 80, 'max' => 100]]));
    $this->post("/admin/infusions/{$drug->id}/publish")->assertRedirect();
    expect($drug->fresh()->status)->toBe('published');
});

it('réserve le catalogue aux comptes autorisés et masque les brouillons des autres centres', function (): void {
    $owner = User::factory()->for(Organization::factory())->create();
    $reader = User::factory()->for(Organization::factory())->create();
    $this->get('/admin/infusions/catalog')->assertRedirect('/login');
    $this->actingAs($owner)->post('/admin/infusions', infusionInput())->assertRedirect();
    $published = InfusionDrug::firstOrFail();
    $this->post("/admin/infusions/{$published->id}/publish")->assertRedirect();
    $this->post("/admin/infusions/{$published->id}/revise")->assertRedirect();
    $draft = InfusionDrug::where('status', 'draft')->firstOrFail();

    $this->actingAs($reader)->get('/admin/infusions/catalog')->assertInertia(fn ($page) => $page
        ->component('admin/infusions/catalog')->has('infusions.data', 1)
        ->where('infusions.data.0.id', $published->id));
    $this->get("/admin/infusions/{$published->id}")->assertInertia(fn ($page) => $page
        ->has('infusion.versions', 1)->where('canManage', false));
    $this->get("/admin/infusions/{$draft->id}")->assertNotFound();
    $this->post("/admin/infusions/{$draft->id}/copy")->assertNotFound();
    $this->post("/admin/infusions/{$draft->id}/publish")->assertNotFound();
    $this->delete("/admin/infusions/{$published->id}")->assertForbidden();
});

it('refuse les plages et les préparations qui empêcheraient un calcul valide', function (array $changes, string $error): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $input = infusionInput();
    $input['preparations'][0] = array_replace($input['preparations'][0], $changes);

    $this->actingAs($administrator)->post('/admin/infusions', $input)->assertSessionHasErrors($error);

    expect(InfusionDrug::count())->toBe(0);
})->with([
    'maximum égal au minimum' => [['min_weight' => 10, 'max_weight' => 10], 'preparations.0.max_weight'],
    'poids négatif' => [['min_weight' => -1], 'preparations.0.min_weight'],
    'concentration nulle' => [['concentration' => 0], 'preparations.0.concentration'],
    'unité inconnue' => [['concentration_unit' => 'g'], 'preparations.0.concentration_unit'],
    'volume nul' => [['total_volume' => 0], 'preparations.0.total_volume'],
    'propriétaire injecté' => [['infusion_drug_id' => 999], 'preparations.0'],
]);

it('restaure une publication supprimée même lorsqu’une révision attend encore en brouillon', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $this->actingAs($administrator)->post('/admin/infusions', infusionInput())->assertRedirect();
    $source = InfusionDrug::firstOrFail();
    $this->post("/admin/infusions/{$source->id}/publish")->assertRedirect();
    $this->post("/admin/infusions/{$source->id}/revise")->assertRedirect();
    $draft = InfusionDrug::where('status', 'draft')->firstOrFail();
    $this->delete("/admin/infusions/{$source->id}")->assertRedirect();
    $this->get('/admin/infusions?deleted=1')->assertInertia(fn ($page) => $page->where('infusions.data.0.canRestore', true));

    $this->post("/admin/infusions/{$source->id}/restore")->assertRedirect();

    expect(InfusionDrug::published()->pluck('id')->all())->toBe([$source->id]);
    $this->post("/admin/infusions/{$draft->id}/publish")->assertRedirect();
    expect(InfusionDrug::published()->pluck('id')->all())->toBe([$draft->id]);
});

it('refuse de restaurer une version remplacée et interdit la restauration depuis un autre centre', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $outsider = User::factory()->for(Organization::factory())->create();
    $this->actingAs($administrator)->post('/admin/infusions', infusionInput())->assertRedirect();
    $source = InfusionDrug::firstOrFail();
    $this->post("/admin/infusions/{$source->id}/publish")->assertRedirect();
    $this->post("/admin/infusions/{$source->id}/revise")->assertRedirect();
    $draft = InfusionDrug::where('status', 'draft')->firstOrFail();
    $this->post("/admin/infusions/{$draft->id}/publish")->assertRedirect();
    $this->delete("/admin/infusions/{$source->id}")->assertRedirect();

    $this->post("/admin/infusions/{$source->id}/restore")->assertConflict();
    $this->actingAs($outsider)->post("/admin/infusions/{$source->id}/restore")->assertForbidden();

    $this->assertSoftDeleted($source);
    expect(InfusionDrug::published()->pluck('id')->all())->toBe([$draft->id]);
});

it('refuse de restaurer un ancien brouillon lorsqu’une nouvelle révision existe', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $this->actingAs($administrator)->post('/admin/infusions', infusionInput())->assertRedirect();
    $source = InfusionDrug::firstOrFail();
    $this->post("/admin/infusions/{$source->id}/publish")->assertRedirect();
    $this->post("/admin/infusions/{$source->id}/revise")->assertRedirect();
    $oldDraft = InfusionDrug::where('status', 'draft')->firstOrFail();
    $this->delete("/admin/infusions/{$oldDraft->id}")->assertRedirect();
    $this->post("/admin/infusions/{$source->id}/revise")->assertRedirect();
    $newDraft = InfusionDrug::where('status', 'draft')->firstOrFail();

    $this->post("/admin/infusions/{$oldDraft->id}/restore")->assertConflict();

    $this->assertSoftDeleted($oldDraft);
    expect($newDraft->fresh()->status)->toBe('draft');
    expect(InfusionDrug::published()->pluck('id')->all())->toBe([$source->id]);
});

it('enregistre une unité combinée sans catégorie ni ordre saisis', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $input = infusionInput(['dose_unit' => 'mcg/min']);
    unset($input['debit_dose_unit'], $input['debit_time_unit'], $input['type'], $input['order']);

    $this->actingAs($administrator)->post('/admin/infusions', $input)->assertRedirect();

    $drug = InfusionDrug::firstOrFail();
    expect($drug->dose_per_kg)->toBeFalse();
    expect($drug->debit_dose_unit)->toBe('mcg');
    expect($drug->debit_time_unit)->toBe('min');
    $this->get("/admin/infusions/{$drug->id}/edit")->assertInertia(fn ($page) => $page
        ->where('infusion.dose_unit', 'mcg/min'));
});

it('accepte les unités combinées et déduit leurs composantes malgré des champs internes injectés', function (string $unit, string $doseUnit, string $timeUnit, bool $perKg): void {
    $administrator = User::factory()->for(Organization::factory())->create();

    $this->actingAs($administrator)->post('/admin/infusions', infusionInput([
        'dose_unit' => $unit, 'debit_dose_unit' => 'invalide', 'debit_time_unit' => 'invalide', 'dose_per_kg' => ! $perKg,
    ]))->assertRedirect()->assertSessionHasNoErrors();

    $drug = InfusionDrug::firstOrFail();
    expect($drug->debit_dose_unit)->toBe($doseUnit);
    expect($drug->debit_time_unit)->toBe($timeUnit);
    expect($drug->dose_per_kg)->toBe($perKg);
})->with([
    ['mg/kg/h', 'mg', 'h', true], ['mg/kg/min', 'mg', 'min', true],
    ['mg/h', 'mg', 'h', false], ['mg/min', 'mg', 'min', false],
    ['mcg/kg/h', 'mcg', 'h', true], ['mcg/kg/min', 'mcg', 'min', true],
    ['mcg/h', 'mcg', 'h', false], ['mcg/min', 'mcg', 'min', false],
    ['unité/kg/h', 'unité', 'h', true], ['unité/kg/min', 'unité', 'min', true],
    ['unité/h', 'unité', 'h', false], ['unité/min', 'unité', 'min', false],
    ['mU/kg/h', 'mU', 'h', true], ['mU/kg/min', 'mU', 'min', true],
    ['mU/h', 'mU', 'h', false], ['mU/min', 'mU', 'min', false],
]);

it('refuse une unité combinée non prise en charge', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();

    $this->actingAs($administrator)->post('/admin/infusions', infusionInput(['dose_unit' => 'mg/kg/jour']))
        ->assertSessionHasErrors('dose_unit');

    expect(InfusionDrug::count())->toBe(0);
});

it('préserve la catégorie et l’ordre existants en modifiant une unité de dose', function (): void {
    $organization = Organization::factory()->create();
    $administrator = User::factory()->for($organization)->create();
    $drug = InfusionDrug::factory()->for($organization)->create(['status' => 'draft', 'type' => 2, 'order' => 7]);

    $this->actingAs($administrator)->put("/admin/infusions/{$drug->id}", infusionInput([
        'dose_unit' => 'mg/h', 'type' => 3, 'order' => 99,
    ]))->assertRedirect()->assertSessionHasNoErrors();

    expect($drug->fresh()->type)->toBe(2);
    expect($drug->fresh()->order)->toBe(7);
    expect($drug->fresh()->doseUnit())->toBe('mg/h');
});
