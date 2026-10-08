<?php

use App\Models\Bolus;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

describe('organization drafts', function (): void {
    it('shows a pending revision draft next to its published bolus', function (): void {
        $organization = Organization::factory()->create();
        $administrator = User::factory()->for($organization)->create();
        $published = Bolus::factory()->for($organization)->create(['name' => 'Recette publiée']);
        $draft = Bolus::factory()->for($organization)->create([
            'name' => 'Recette publiée',
            'recipe_id' => $published->recipe_id,
            'version' => 2,
            'status' => 'draft',
            'published_at' => null,
            'supersedes_id' => $published->id,
        ]);

        $this->actingAs($administrator)->get(route('admin.boluses.index'))
            ->assertInertia(fn ($page) => $page
                ->component('admin/boluses/index')
                ->where('boluses.data.0.id', $draft->id)
                ->where('boluses.data.0.pendingDraftId', null)
                ->where('boluses.data.1.id', $published->id)
                ->where('boluses.data.1.pendingDraftId', $draft->id));
    });

    it('saves an empty instructions field as an empty string', function (): void {
        $organization = Organization::factory()->create();
        $administrator = User::factory()->for($organization)->create();
        $bolus = Bolus::factory()->for($organization)->create(['status' => 'draft', 'published_at' => null]);

        $this->actingAs($administrator)
            ->put(route('admin.boluses.update', $bolus), bolusInput(['instructions' => '']))
            ->assertRedirect();

        expect($bolus->fresh()->instructions)->toBe('');
    });

    it('lets an administrator create and preview a draft bolus only in their organization', function (): void {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $administrator = User::factory()->for($organization)->create();
        $otherAdministrator = User::factory()->for($otherOrganization)->create();

        $this->actingAs($administrator)->post(route('admin.boluses.store'), [
            'name' => 'Bolus de test',
            'brand_name' => 'Marque',
            'unit' => 'mg',
            'commercial_concentration' => 10,
            'dosage' => 2,
            'minimum_dose' => 0,
            'maximum_dose' => 100,
            'dose_precision' => 1,
            'volume_precision' => 1,
            'type' => 1,
            'min_weight' => 0,
            'max_weight' => 999,
            'instructions' => 'À administrer lentement.',
            'asterisk' => false,
        ])->assertRedirect();

        $bolus = $organization->boluses()->firstOrFail();

        $this->get(route('admin.boluses.show', $bolus))
            ->assertInertia(fn ($page) => $page
                ->component('admin/boluses/show')
                ->where('bolus.status', 'draft')
                ->where('bolus.name', 'Bolus de test'));

        $this->actingAs($otherAdministrator)
            ->get(route('admin.boluses.show', $bolus))
            ->assertNotFound();

        $this->actingAs($otherAdministrator)
            ->put(route('admin.boluses.update', $bolus), bolusInput(['name' => 'Modification interdite']))
            ->assertNotFound();

        expect($bolus->fresh()->name)->toBe('Bolus de test');
    });

    it('copies a published bolus into the current organization as an independent draft', function (): void {
        $sourceOrganization = Organization::factory()->create();
        $destinationOrganization = Organization::factory()->create();
        $administrator = User::factory()->for($destinationOrganization)->create();
        $source = Bolus::factory()->for($sourceOrganization)->create(['name' => 'Bolus partagé', 'dosage' => 3]);

        $this->actingAs($administrator)->get(route('admin.boluses.show', $source))
            ->assertInertia(fn ($page) => $page
                ->component('admin/boluses/show')
                ->where('canManage', false)
                ->where('canCopy', true));

        $this->actingAs($administrator)->post(route('admin.boluses.copy', $source))->assertRedirect();

        $copy = $destinationOrganization->boluses()->firstOrFail();
        expect($copy->status)->toBe('draft')
            ->and($copy->recipe_id)->not->toBe($source->recipe_id)
            ->and($copy->copied_from_id)->toBe($source->id)
            ->and($copy->dosage)->toBe(3.0);

        $this->put(route('admin.boluses.update', $copy), bolusInput(['dosage' => 4]))->assertRedirect();

        expect($source->fresh()->dosage)->toBe(3.0);
        expect($copy->fresh()->dosage)->toBe(4.0);
    });
});

describe('catalog and publications', function (): void {
    it('requires an authenticated administrator to open the catalog', function (): void {
        $this->get(route('admin.boluses.catalog'))->assertRedirect(route('login'));
    });

    it('shows only the active published versions of every organization in the catalog', function (): void {
        $firstOrganization = Organization::factory()->create();
        $secondOrganization = Organization::factory()->create();
        $administrator = User::factory()->for($firstOrganization)->create();
        $firstPublished = Bolus::factory()->for($firstOrganization)->create(['name' => 'Bolus A']);
        $secondPublished = Bolus::factory()->for($secondOrganization)->create(['name' => 'Bolus B']);
        $draft = Bolus::factory()->for($secondOrganization)->create(['name' => 'Brouillon caché', 'status' => 'draft', 'published_at' => null]);
        $superseded = Bolus::factory()->for($secondOrganization)->create(['name' => 'Ancienne version', 'superseded_at' => now()]);

        $this->actingAs($administrator)->get(route('admin.boluses.catalog'))
            ->assertInertia(fn ($page) => $page
                ->component('admin/boluses/catalog')
                ->has('boluses.data', 2)
                ->where('boluses.data.0.id', $firstPublished->id)
                ->where('boluses.data.1.id', $secondPublished->id));

        expect($draft->fresh()->status)->toBe('draft');
        expect($superseded->fresh()->superseded_at)->not->toBeNull();
    });

    it('uses only published boluses in the weight-based calculator', function (): void {
        $organization = Organization::factory()->create();
        $administrator = User::factory()->for($organization)->create();
        $published = Bolus::factory()->for($organization)->create(['name' => 'Dose publiée']);
        Bolus::factory()->for($organization)->create([
            'name' => 'Dose en brouillon',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->actingAs($administrator)
            ->withSession(['app.dosingWeight' => 10])
            ->get('/bolus')
            ->assertInertia(fn ($page) => $page
                ->component('calculator/bolus')
                ->has('boluses', 1)
                ->where('boluses.0.name', $published->name));
    });

    it('keeps a published version active until its replacement draft is published', function (): void {
        $organization = Organization::factory()->create();
        $administrator = User::factory()->for($organization)->create();
        $published = Bolus::factory()->for($organization)->create(['name' => 'Version initiale']);

        $this->actingAs($administrator)->get(route('admin.boluses.show', $published))
            ->assertInertia(fn ($page) => $page
                ->component('admin/boluses/show')
                ->where('canManage', true)
                ->where('canCopy', false));

        $this->actingAs($administrator)->post(route('admin.boluses.revise', $published))->assertRedirect();

        $draft = $organization->boluses()->where('status', 'draft')->firstOrFail();
        expect(Bolus::published()->where('recipe_id', $published->recipe_id)->sole()->is($published))->toBeTrue();

        $this->get(route('admin.boluses.show', $published))
            ->assertInertia(fn ($page) => $page
                ->component('admin/boluses/show')
                ->where('pendingDraftId', $draft->id));

        $this->post(route('admin.boluses.revise', $published))->assertStatus(409);
        expect($organization->boluses()->where('recipe_id', $published->recipe_id)->where('status', 'draft')->count())->toBe(1);

        $this->put(route('admin.boluses.update', $draft), bolusInput(['dosage' => 5]))->assertRedirect();
        $this->post(route('admin.boluses.publish', $draft))->assertRedirect();

        expect(Bolus::published()->where('recipe_id', $published->recipe_id)->sole()->is($draft->fresh()))->toBeTrue();
        expect($published->fresh()->superseded_at)->not->toBeNull();
        expect($draft->fresh()->published_by)->toBe($administrator->id);
        expect($draft->fresh()->created_by)->toBe($administrator->id);
        expect($draft->activities()->pluck('action')->all())->toContain('revision_created', 'updated', 'published');
    });

    it('allows a new revision after the pending draft is deleted', function (): void {
        $organization = Organization::factory()->create();
        $administrator = User::factory()->for($organization)->create();
        $published = Bolus::factory()->for($organization)->create(['name' => 'Version initiale']);

        $this->actingAs($administrator)->post(route('admin.boluses.revise', $published))->assertRedirect();
        $pendingDraft = $organization->boluses()->where('status', 'draft')->firstOrFail();

        $this->delete(route('admin.boluses.destroy', $pendingDraft))->assertRedirect();
        $this->post(route('admin.boluses.revise', $published))->assertRedirect();

        expect($organization->boluses()->where('recipe_id', $published->recipe_id)->where('status', 'draft')->value('version'))->toBe(3);
        expect(Bolus::withTrashed()->where('recipe_id', $published->recipe_id)->where('status', 'draft')->count())->toBe(2);
    });
});

describe('legacy bolus attribution', function (): void {
    it('assigns existing boluses to the Estrie organization and publishes them', function (): void {
        $previousDefaultConnection = config('database.default');
        config([
            'database.connections.legacy_migration_test' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'database.default' => 'legacy_migration_test',
        ]);
        DB::purge('legacy_migration_test');

        try {
            Schema::create('organizations', function ($table): void {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
            Schema::create('users', function ($table): void {
                $table->increments('id');
            });
            Schema::create('boluses', function ($table): void {
                $table->increments('id');
                $table->timestamps();
            });

            DB::table('boluses')->insert([
                ['created_at' => now(), 'updated_at' => now()],
                ['created_at' => now(), 'updated_at' => now()],
            ]);

            $migration = require database_path('migrations/2026_10_08_103700_add_recipe_management_to_boluses_table.php');
            $migration->up();

            $legacyOrganization = DB::table('organizations')->where('name', 'CIUSSS de l’Estrie–CHUS')->first();
            $legacyBoluses = DB::table('boluses')->orderBy('id')->get();

            expect($legacyOrganization)->not->toBeNull();
            expect($legacyBoluses)->toHaveCount(2);
            expect($legacyBoluses[0]->organization_id)->toBe($legacyOrganization->id);
            expect($legacyBoluses[1]->organization_id)->toBe($legacyOrganization->id);
            expect($legacyBoluses[0]->status)->toBe('published');
            expect($legacyBoluses[1]->status)->toBe('published');
            expect($legacyBoluses[0]->published_at)->not->toBeNull();
            expect($legacyBoluses[1]->published_at)->not->toBeNull();
            expect($legacyBoluses[0]->recipe_id)->not->toBe($legacyBoluses[1]->recipe_id);
        } finally {
            DB::disconnect('legacy_migration_test');
            DB::purge('legacy_migration_test');
            config(['database.default' => $previousDefaultConnection]);
        }
    });
});

describe('deleted boluses', function (): void {
    it('soft deletes and restores a bolus while keeping both actions in its history', function (): void {
        $organization = Organization::factory()->create();
        $administrator = User::factory()->for($organization)->create();
        $bolus = Bolus::factory()->for($organization)->create(['status' => 'draft', 'published_at' => null]);

        $this->actingAs($administrator)->delete(route('admin.boluses.destroy', $bolus))->assertRedirect();

        expect(Bolus::find($bolus->id))->toBeNull();
        $this->post(route('admin.boluses.restore', $bolus))->assertRedirect(route('admin.boluses.show', $bolus));

        expect(Bolus::findOrFail($bolus->id)->trashed())->toBeFalse();
        expect($bolus->activities()->pluck('action')->all())->toContain('deleted', 'restored');
    });
});

/**
 * @param  array<string, bool|float|int|string>  $overrides
 * @return array<string, bool|float|int|string>
 */
function bolusInput(array $overrides = []): array
{
    return array_merge([
        'name' => 'Bolus modifié',
        'brand_name' => 'Marque',
        'unit' => 'mg',
        'commercial_concentration' => 10,
        'dosage' => 2,
        'minimum_dose' => 0,
        'maximum_dose' => 100,
        'dose_precision' => 1,
        'volume_precision' => 1,
        'type' => 1,
        'min_weight' => 0,
        'max_weight' => 999,
        'instructions' => 'À administrer lentement.',
        'asterisk' => false,
    ], $overrides);
}
