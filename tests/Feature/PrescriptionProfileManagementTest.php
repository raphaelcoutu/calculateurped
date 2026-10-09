<?php

use App\Models\Bolus;
use App\Models\InfusionConcentration;
use App\Models\InfusionDrug;
use App\Models\Organization;
use App\Models\PrescriptionProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('liste sans doublons les profils utilisant la version consultée de la recette', function (string $model, string $path, string $column): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $recipe = $model::factory()->create(['organization_id' => $administrator->organization_id]);
    $replacement = $model::factory()->create(['organization_id' => $administrator->organization_id, 'recipe_id' => $recipe->recipe_id, 'version' => 2]);
    $profiles = PrescriptionProfile::factory()->count(4)->sequence(
        ['name' => 'A Brouillon', 'status' => 'draft'],
        ['name' => 'B Dépublié', 'status' => 'unpublished'],
        ['name' => 'C Publié', 'status' => 'published'],
        ['name' => 'D Remplacé', 'status' => 'published', 'superseded_at' => now()],
    )->create(['organization_id' => $administrator->organization_id]);
    foreach ($profiles as $profile) {
        $section = $profile->sections()->create(['name' => 'Section', 'position' => 0]);
        $section->items()->create([$column => $recipe->id, 'position' => 0]);
        $section->items()->create([$column => $recipe->id, 'position' => 1]);
    }
    $deleted = PrescriptionProfile::factory()->create(['organization_id' => $administrator->organization_id]);
    $deleted->sections()->create(['name' => 'Section', 'position' => 0])->items()->create([$column => $recipe->id, 'position' => 0]);
    $deleted->delete();
    $foreign = PrescriptionProfile::factory()->create();
    $foreign->sections()->create(['name' => 'Section', 'position' => 0])->items()->create([$column => $recipe->id, 'position' => 0]);
    $otherVersion = PrescriptionProfile::factory()->create(['organization_id' => $administrator->organization_id]);
    $otherVersion->sections()->create(['name' => 'Section', 'position' => 0])->items()->create([$column => $replacement->id, 'position' => 0]);

    $this->actingAs($administrator)->get("/admin/{$path}/{$recipe->id}")->assertInertia(fn ($page) => $page
        ->has('usingProfiles', 4)
        ->where('usingProfiles.0.id', $profiles[0]->id)->where('usingProfiles.0.name', 'A Brouillon')->where('usingProfiles.0.status', 'draft')
        ->where('usingProfiles.0.version', 1)->where('usingProfiles.0.url', route('admin.profiles.show', $profiles[0]))
        ->where('usingProfiles.1.id', $profiles[1]->id)->where('usingProfiles.1.status', 'unpublished')
        ->where('usingProfiles.2.id', $profiles[2]->id)->where('usingProfiles.2.status', 'published')->where('usingProfiles.2.supersededAt', null)
        ->where('usingProfiles.3.id', $profiles[3]->id)->where('usingProfiles.3.supersededAt', $profiles[3]->superseded_at->toIso8601String()));
})->with([
    'bolus' => [Bolus::class, 'boluses', 'bolus_id'],
    'perfusion' => [InfusionDrug::class, 'infusions', 'infusion_drug_id'],
]);

it('ne révèle pas les profils privés lors de la consultation du catalogue d’un autre centre', function (string $model, string $path, string $column): void {
    $recipe = $model::factory()->create();
    $profile = PrescriptionProfile::factory()->create(['organization_id' => $recipe->organization_id]);
    $profile->sections()->create(['name' => 'Section', 'position' => 0])->items()->create([$column => $recipe->id, 'position' => 0]);
    $visitor = User::factory()->for(Organization::factory())->create();

    $this->actingAs($visitor)->get("/admin/{$path}/{$recipe->id}")->assertInertia(fn ($page) => $page->has('usingProfiles', 0));
})->with([
    'bolus' => [Bolus::class, 'boluses', 'bolus_id'],
    'perfusion' => [InfusionDrug::class, 'infusions', 'infusion_drug_id'],
]);

it('compose plusieurs profils avec sections ordonnées et recettes répétées dans son centre', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id]);
    $infusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id]);
    $input = ['name' => 'Urgence', 'sections' => [
        ['name' => 'Première', 'items' => [
            ['type' => 'infusion', 'recipe_id' => $infusion->id],
            ['type' => 'bolus', 'recipe_id' => $bolus->id],
            ['type' => 'bolus', 'recipe_id' => $bolus->id],
        ]],
        ['name' => 'Seconde', 'items' => []],
    ]];

    $this->actingAs($administrator)->post('/admin/profiles', [...$input, 'status' => 'published', 'organization_id' => 999])->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();
    $this->assertDatabaseHas('prescription_profiles', ['id' => $profile->id, 'organization_id' => $administrator->organization_id, 'created_by' => $administrator->id]);
    $this->get("/admin/profiles/{$profile->id}")->assertInertia(fn ($page) => $page
        ->component('admin/profiles/show')->where('profile.name', 'Urgence')
        ->where('profile.sections.0.name', 'Première')->where('profile.sections.1.name', 'Seconde')
        ->has('profile.sections.0.items', 3)->where('profile.sections.0.items.0.type', 'infusion')
        ->where('profile.sections.0.items.1.recipe_id', $bolus->id)->where('profile.sections.0.items.2.recipe_id', $bolus->id));
    $this->post('/admin/profiles', ['name' => 'Autre', 'sections' => [['name' => 'Section', 'items' => []]]])->assertRedirect();
    expect(PrescriptionProfile::where('organization_id', $administrator->organization_id)->count())->toBe(2);
    $this->assertDatabaseHas('prescription_activity', ['prescription_profile_id' => $profile->id, 'action' => 'created', 'user_id' => $administrator->id]);
});

it('refuse de publier avec des recettes en brouillon et conserve la version active pendant la révision', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id]);
    $input = ['name' => 'Urgence', 'sections' => [['name' => 'Bolus', 'items' => [['type' => 'bolus', 'recipe_id' => $bolus->id]]]]];
    $this->actingAs($administrator)->post('/admin/profiles', $input)->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();

    $this->post("/admin/boluses/{$bolus->id}/unpublish")->assertRedirect();
    $this->post("/admin/profiles/{$profile->id}/publish")->assertSessionHasErrors(['recipes']);
    expect($profile->fresh()->status)->toBe('draft');
    $this->post("/admin/boluses/{$bolus->id}/publish")->assertRedirect();
    $this->post("/admin/profiles/{$profile->id}/publish")->assertRedirect();
    $this->post("/admin/profiles/{$profile->id}/default")->assertRedirect();
    $this->post("/admin/profiles/{$profile->id}/revise")->assertRedirect();
    $draft = PrescriptionProfile::where('status', 'draft')->firstOrFail();
    $this->post("/admin/profiles/{$profile->id}/revise")->assertConflict();
    $this->put("/admin/profiles/{$profile->id}", $input)->assertNotFound();
    $this->put("/admin/profiles/{$draft->id}", [...$input, 'name' => 'Urgence révisée'])->assertRedirect();
    expect($profile->fresh()->name)->toBe('Urgence');
    expect(PrescriptionProfile::published()->pluck('id')->all())->toBe([$profile->id]);
    expect($administrator->organization->fresh()->default_prescription_profile_id)->toBe($profile->id);

    $this->post("/admin/profiles/{$draft->id}/publish")->assertRedirect();

    expect(PrescriptionProfile::published()->pluck('id')->all())->toBe([$draft->id]);
    expect($administrator->organization->fresh()->default_prescription_profile_id)->toBe($draft->id);
    $this->assertDatabaseHas('prescription_profiles', ['id' => $draft->id, 'version' => 2, 'published_by' => $administrator->id]);
    $this->assertDatabaseHas('prescription_activity', ['prescription_profile_id' => $draft->id, 'action' => 'published', 'user_id' => $administrator->id]);
});

it('protège les recettes publiées puis les libère après dépublication du profil', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id]);
    $infusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id]);
    $input = ['name' => 'Mixte', 'sections' => [['name' => 'Section', 'items' => [
        ['type' => 'bolus', 'recipe_id' => $bolus->id], ['type' => 'infusion', 'recipe_id' => $infusion->id],
    ]]]];
    $this->actingAs($administrator)->post('/admin/profiles', $input)->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();
    $this->post("/admin/profiles/{$profile->id}/publish")->assertRedirect();
    $this->post("/admin/profiles/{$profile->id}/default")->assertRedirect();

    $this->delete("/admin/boluses/{$bolus->id}")->assertSessionHasErrors('recipe');
    $this->delete("/admin/infusions/{$infusion->id}")->assertSessionHasErrors('recipe');
    $this->post("/admin/boluses/{$bolus->id}/unpublish")->assertSessionHasErrors('recipe');
    $this->post("/admin/infusions/{$infusion->id}/unpublish")->assertSessionHasErrors('recipe');
    expect($bolus->fresh()->status)->toBe('published');
    expect($infusion->fresh()->status)->toBe('published');
    $this->post("/admin/profiles/{$profile->id}/unpublish")->assertRedirect();
    expect(PrescriptionProfile::published()->count())->toBe(0);
    expect($administrator->organization->fresh()->default_prescription_profile_id)->toBeNull();
    $this->put("/admin/profiles/{$profile->id}", $input)->assertRedirect();
    $this->post("/admin/boluses/{$bolus->id}/unpublish")->assertRedirect();
    $this->post("/admin/infusions/{$infusion->id}/unpublish")->assertRedirect();
    expect($bolus->fresh()->status)->toBe('draft');
    expect($infusion->fresh()->status)->toBe('draft');
    $this->post("/admin/profiles/{$profile->id}/publish")->assertSessionHasErrors('recipes');
    $this->delete("/admin/boluses/{$bolus->id}")->assertRedirect();
    $this->delete("/admin/infusions/{$infusion->id}")->assertRedirect();
    $this->assertSoftDeleted($bolus);
    $this->assertSoftDeleted($infusion);
});

it('change le profil par défaut et restaure un profil supprimé sans réactiver sa publication', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $first = PrescriptionProfile::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'published']);
    $second = PrescriptionProfile::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'published']);
    $this->actingAs($administrator)->post("/admin/profiles/{$first->id}/default")->assertRedirect();
    $this->post("/admin/profiles/{$second->id}/default")->assertRedirect();
    expect($administrator->organization->fresh()->default_prescription_profile_id)->toBe($second->id);

    $this->delete("/admin/profiles/{$second->id}")->assertRedirect();

    $this->assertSoftDeleted($second);
    expect($administrator->organization->fresh()->default_prescription_profile_id)->toBeNull();
    $this->get('/admin/profiles?deleted=1')->assertInertia(fn ($page) => $page->has('profiles.data', 1)->where('profiles.data.0.id', $second->id));
    $this->post("/admin/profiles/{$second->id}/restore")->assertRedirect();
    expect($second->fresh()->status)->toBe('unpublished');
    $this->assertDatabaseHas('prescription_activity', ['prescription_profile_id' => $first->id, 'action' => 'default_removed']);
    $this->assertDatabaseHas('prescription_activity', ['prescription_profile_id' => $second->id, 'action' => 'deleted']);
    $this->assertDatabaseHas('prescription_activity', ['prescription_profile_id' => $second->id, 'action' => 'restored']);
    $this->post("/admin/profiles/{$second->id}/default")->assertNotFound();
    $this->post("/admin/profiles/{$second->id}/publish")->assertRedirect();
    expect($second->fresh()->status)->toBe('published');
});

it('prévisualise un brouillon avec les calculs existants sans modifier les données de session', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'name' => 'Bolus fictif', 'asterisk' => true, 'instructions' => 'Instruction bolus']);
    $infusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id, 'name' => 'Perfusion fictive', 'brand_name' => '']);
    InfusionConcentration::factory()->create(['infusion_drug_id' => $infusion->id, 'min_weight' => 0, 'max_weight' => null]);
    $this->actingAs($administrator)->post('/admin/profiles', ['name' => 'Mixte', 'sections' => [['name' => 'Urgence', 'items' => [
        ['type' => 'infusion', 'recipe_id' => $infusion->id], ['type' => 'bolus', 'recipe_id' => $bolus->id], ['type' => 'bolus', 'recipe_id' => $bolus->id],
    ]]]])->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();

    $this->post("/admin/boluses/{$bolus->id}/unpublish")->assertRedirect();
    $this->post("/admin/infusions/{$infusion->id}/unpublish")->assertRedirect();

    $response = $this->withSession(['app' => ['name' => 'Patient courant', 'id' => 'Réel', 'weight' => 30, 'dosingWeight' => 30]])
        ->get("/admin/profiles/{$profile->id}/preview?weight=10&name=Patient%20fictif&patient_id=TEST");

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    expect($response->getContent())->toStartWith('%PDF-');
    $text = prescriptionPdfText($response->getContent());
    expect($text)->toContain('Patient fictif', '#TEST', '10 kg', 'Urgence', '10 mg', '1 mL', '1 - 5 mL/h');
    expect(substr_count($text, 'Bolus fictif'))->toBe(2);
    expect(strpos($text, 'Perfusion fictive'))->toBeLessThan(strpos($text, 'Bolus fictif'));
    expect($text)->toContain('50 mL', '15-30 minutes');
    expect($text)->not->toContain('Patient courant', '#Réel', '30 kg');
    $response->assertSessionHas('app.name', 'Patient courant')->assertSessionHas('app.weight', 30)->assertSessionHas('app.id', 'Réel');
    $this->get("/admin/profiles/{$profile->id}/preview?weight=0")->assertSessionHasErrors('weight');
});

it('isole les profils et la prévisualisation entre centres', function (): void {
    $owner = User::factory()->for(Organization::factory())->create();
    $other = User::factory()->for(Organization::factory())->create();
    $profile = PrescriptionProfile::factory()->create(['organization_id' => $owner->organization_id, 'status' => 'published']);
    $this->actingAs($other)->get('/admin/profiles')->assertInertia(fn ($page) => $page->has('profiles.data', 0));
    $this->get("/admin/profiles/{$profile->id}")->assertNotFound();
    $this->get("/admin/profiles/{$profile->id}/edit")->assertNotFound();
    $this->get("/admin/profiles/{$profile->id}/preview?weight=10")->assertNotFound();
    $this->put("/admin/profiles/{$profile->id}", ['name' => 'Interdit', 'sections' => [['name' => 'Section', 'items' => []]]])->assertNotFound();
    foreach (['publish', 'unpublish', 'revise', 'default', 'restore'] as $action) {
        $this->post("/admin/profiles/{$profile->id}/{$action}")->assertNotFound();
    }
    $this->delete("/admin/profiles/{$profile->id}")->assertNotFound();
    expect($profile->fresh()->name)->toBe($profile->name);
    expect($profile->fresh()->status)->toBe('published');
    expect($profile->activities()->count())->toBe(0);
    $this->actingAs(User::factory()->create(['is_superuser' => true]))->get('/admin/profiles')->assertForbidden();
});

it('refuse les recettes étrangères ou supprimées et les structures invalides sans enregistrer de profil', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $foreign = Bolus::factory()->create();
    $deleted = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id]);
    $deleted->delete();
    $this->actingAs($administrator)->post('/admin/profiles', [])->assertSessionHasErrors(['name', 'sections']);
    foreach ([['type' => 'bolus', 'recipe_id' => $foreign->id], ['type' => 'infusion', 'recipe_id' => $deleted->id]] as $item) {
        $this->post('/admin/profiles', ['name' => 'Interdit', 'sections' => [['name' => 'Section', 'items' => [$item]]]])
            ->assertSessionHasErrors(['sections.0.items.0.recipe_id' => 'Choisissez une recette non supprimée de votre centre.']);
    }
    $this->post('/admin/profiles', ['name' => 'Interdit', 'sections' => [['name' => '', 'items' => [['type' => 'inconnu', 'recipe_id' => 1]]]]])
        ->assertSessionHasErrors(['sections.0.name', 'sections.0.items.0.type']);
    expect(PrescriptionProfile::count())->toBe(0);
});

it('exige une connexion pour gérer ou prévisualiser les profils', function (): void {
    $profile = PrescriptionProfile::factory()->create();
    $this->get('/admin/profiles')->assertRedirect('/login');
    $this->post('/admin/profiles', [])->assertRedirect('/login');
    $this->get("/admin/profiles/{$profile->id}/preview?weight=10")->assertRedirect('/login');
});

it('refuse la publication et la restauration d’une ancienne version remplacée', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $profile = PrescriptionProfile::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'published']);
    $this->actingAs($administrator)->post("/admin/profiles/{$profile->id}/revise")->assertRedirect();
    $draft = PrescriptionProfile::where('status', 'draft')->firstOrFail();
    $this->post("/admin/profiles/{$profile->id}/unpublish")->assertRedirect();
    $this->post("/admin/profiles/{$draft->id}/publish")->assertRedirect();
    $this->post("/admin/profiles/{$profile->id}/publish")->assertNotFound();
    $this->delete("/admin/profiles/{$profile->id}")->assertRedirect();
    $this->post("/admin/profiles/{$profile->id}/restore")->assertConflict();
    $this->assertSoftDeleted($profile);
});

/** Extract the literal text operators from PDF content streams using the existing standard PDF fonts. */
function prescriptionPdfText(string $pdf): string
{
    preg_match_all('/<<(.*?)>>\s*stream\r?\n(.*?)\r?\nendstream/s', $pdf, $streams, PREG_SET_ORDER);
    $text = [];
    foreach ($streams as $stream) {
        $content = str_contains($stream[1], '/FlateDecode') ? gzuncompress($stream[2]) : $stream[2];
        if ($content === false || ! str_contains($content, 'BT ')) {
            continue;
        }
        preg_match_all('/\\(((?:\\\\.|[^\\\\)])*)\\)/s', $content, $literals);
        foreach ($literals[1] as $literal) {
            $text[] = mb_convert_encoding(stripcslashes($literal), 'UTF-8', 'Windows-1252');
        }
    }

    return implode(' ', $text);
}

it('conserve la note de dilution au-dessus de quinze kilogrammes et signale les recettes indisponibles', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'name' => 'Bolus avec note', 'asterisk' => true]);
    $infusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id, 'name' => 'Perfusion hors plage']);
    InfusionConcentration::factory()->create(['infusion_drug_id' => $infusion->id, 'min_weight' => 0, 'max_weight' => 10]);
    $this->actingAs($administrator)->post('/admin/profiles', ['name' => 'Profil', 'sections' => [['name' => 'Section', 'items' => [
        ['type' => 'bolus', 'recipe_id' => $bolus->id], ['type' => 'infusion', 'recipe_id' => $infusion->id],
    ]]]])->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();

    $response = $this->get("/admin/profiles/{$profile->id}/preview?weight=20");

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    $text = prescriptionPdfText($response->getContent());
    expect($text)->toContain('100 mL', '15-30 minutes', '20 kg', 'Perfusion hors plage', 'aucune préparation compatible');
    expect($text)->not->toContain('50 mL');
    $this->delete("/admin/boluses/{$bolus->id}")->assertRedirect();
    $response = $this->get("/admin/profiles/{$profile->id}/preview?weight=20");
    $response->assertOk();
    expect(prescriptionPdfText($response->getContent()))->toContain('Bolus avec note', 'Recette supprimée');
    $this->post("/admin/profiles/{$profile->id}/publish")->assertSessionHasErrors('recipes');
});

it('propose uniquement les recettes publiées et refuse l’ajout de brouillons', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id]);
    $infusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id]);
    $draftBolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft']);
    $draftInfusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft']);
    $input = ['name' => 'Profil', 'sections' => [['name' => 'Section', 'items' => [['type' => 'bolus', 'recipe_id' => $bolus->id]]]]];
    $this->actingAs($administrator)->post('/admin/profiles', $input)->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();

    $this->get('/admin/profiles/create')->assertInertia(fn ($page) => $page->has('recipes', 2)
        ->where('recipes.0.recipe_id', $bolus->id)->where('recipes.1.recipe_id', $infusion->id));
    $this->get("/admin/profiles/{$profile->id}/edit")->assertInertia(fn ($page) => $page->has('recipes', 2));
    foreach ([['type' => 'bolus', 'recipe_id' => $draftBolus->id], ['type' => 'infusion', 'recipe_id' => $draftInfusion->id]] as $item) {
        $invalid = ['name' => 'Interdit', 'sections' => [['name' => 'Section', 'items' => [$item]]]];
        $this->post('/admin/profiles', $invalid)->assertSessionHasErrors('sections.0.items.0.recipe_id');
        $this->put("/admin/profiles/{$profile->id}", $invalid)->assertSessionHasErrors('sections.0.items.0.recipe_id');
    }
    expect(PrescriptionProfile::count())->toBe(1);
    expect($profile->fresh()->name)->toBe('Profil');
});

it('conserve une recette dépubliée déjà choisie mais refuse d’en ajouter une occurrence', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id]);
    $item = ['type' => 'bolus', 'recipe_id' => $bolus->id];
    $input = ['name' => 'Profil', 'sections' => [['name' => 'Section', 'items' => [$item]]]];
    $this->actingAs($administrator)->post('/admin/profiles', $input)->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();
    $this->post("/admin/boluses/{$bolus->id}/unpublish")->assertRedirect();

    $this->put("/admin/profiles/{$profile->id}", [...$input, 'name' => 'Profil modifié'])->assertRedirect();
    $input['sections'][0]['items'][] = $item;
    $this->put("/admin/profiles/{$profile->id}", $input)->assertSessionHasErrors('sections.0.items.1.recipe_id');
    expect($profile->fresh()->name)->toBe('Profil modifié');
    expect($profile->fresh()->profileSnapshot()['sections'][0]['items'])->toHaveCount(1);
});

it('préserve une version de recette remplacée lors de la révision du profil', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id]);
    $input = ['name' => 'Profil', 'sections' => [['name' => 'Section', 'items' => [['type' => 'bolus', 'recipe_id' => $bolus->id]]]]];
    $this->actingAs($administrator)->post('/admin/profiles', $input)->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();
    $this->post("/admin/profiles/{$profile->id}/publish")->assertRedirect();
    $bolus->update(['superseded_at' => now()]);
    $replacement = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'recipe_id' => $bolus->recipe_id, 'version' => 2]);

    $this->post("/admin/profiles/{$profile->id}/revise")->assertRedirect();

    $draft = PrescriptionProfile::where('status', 'draft')->firstOrFail();
    expect($draft->profileSnapshot()['sections'][0]['items'][0]['recipe_id'])->toBe($bolus->id);
    $this->get("/admin/profiles/{$draft->id}/edit")->assertInertia(fn ($page) => $page->has('recipes', 1)->where('recipes.0.recipe_id', $replacement->id));
    $this->put("/admin/profiles/{$draft->id}", $input)->assertRedirect();
});
