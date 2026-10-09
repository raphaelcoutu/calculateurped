<?php

use App\Models\Bolus;
use App\Models\InfusionConcentration;
use App\Models\InfusionDrug;
use App\Models\Organization;
use App\Models\PrescriptionProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('compose plusieurs profils avec sections ordonnées et recettes répétées dans son centre', function (): void {
    $administrator = User::factory()->for(Organization::factory())->create();
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft']);
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
    $this->assertDatabaseHas('prescription_profiles', ['id' => $profile->id, 'organization_id' => $administrator->organization_id, 'status' => 'draft', 'created_by' => $administrator->id]);
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
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft']);
    $input = ['name' => 'Urgence', 'sections' => [['name' => 'Bolus', 'items' => [['type' => 'bolus', 'recipe_id' => $bolus->id]]]]];
    $this->actingAs($administrator)->post('/admin/profiles', $input)->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();

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
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft', 'name' => 'Bolus fictif', 'asterisk' => true, 'instructions' => 'Instruction bolus']);
    $infusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft', 'name' => 'Perfusion fictive', 'brand_name' => '']);
    InfusionConcentration::factory()->create(['infusion_drug_id' => $infusion->id, 'min_weight' => 0, 'max_weight' => null]);
    $this->actingAs($administrator)->post('/admin/profiles', ['name' => 'Mixte', 'sections' => [['name' => 'Urgence', 'items' => [
        ['type' => 'infusion', 'recipe_id' => $infusion->id], ['type' => 'bolus', 'recipe_id' => $bolus->id], ['type' => 'bolus', 'recipe_id' => $bolus->id],
    ]]]])->assertRedirect();
    $profile = PrescriptionProfile::firstOrFail();

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
    $bolus = Bolus::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft', 'name' => 'Bolus avec note', 'asterisk' => true]);
    $infusion = InfusionDrug::factory()->create(['organization_id' => $administrator->organization_id, 'status' => 'draft', 'name' => 'Perfusion hors plage']);
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
