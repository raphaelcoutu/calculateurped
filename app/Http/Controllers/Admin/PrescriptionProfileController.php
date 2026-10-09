<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrescriptionProfileRequest;
use App\Models\Bolus;
use App\Models\InfusionDrug;
use App\Models\Organization;
use App\Models\PrescriptionProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PrescriptionProfileController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', PrescriptionProfile::class);

        return Inertia::render('admin/profiles/index', [
            'profiles' => PrescriptionProfile::where('organization_id', $request->user()->organization_id)
                ->when($request->boolean('deleted'), fn ($query) => $query->onlyTrashed(), fn ($query) => $query->whereNull('superseded_at'))
                ->with(['author', 'publisher', 'organization'])->orderBy('name')->orderByDesc('version')->orderBy('id')
                ->paginate(20)->withQueryString()->through(fn (PrescriptionProfile $profile): array => [
                    'id' => $profile->id, 'name' => $profile->name, 'version' => $profile->version, 'status' => $profile->status,
                    'isDefault' => $profile->organization->default_prescription_profile_id === $profile->id,
                    'author' => $profile->author?->name, 'publishedAt' => $profile->published_at?->toIso8601String(),
                ]),
            'showDeleted' => $request->boolean('deleted'),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', PrescriptionProfile::class);

        return Inertia::render('admin/profiles/form', [
            'profile' => null, 'action' => route('admin.profiles.store'), 'method' => 'post',
            'recipes' => $this->recipeChoices($request->user()->organization_id),
        ]);
    }

    public function store(PrescriptionProfileRequest $request): RedirectResponse
    {
        $profile = DB::transaction(function () use ($request): PrescriptionProfile {
            Organization::whereKey($request->user()->organization_id)->lockForUpdate()->firstOrFail();
            $profile = PrescriptionProfile::create([
                'organization_id' => $request->user()->organization_id,
                'profile_id' => (string) Str::uuid(), 'version' => 1,
                'name' => $request->validated('name'), 'status' => 'draft',
                'created_by' => $request->user()->id,
            ]);
            $this->saveSections($profile, $request->validated('sections'));
            $this->recordActivity($profile, $request, 'created');

            return $profile;
        });

        return redirect()->route('admin.profiles.show', $profile)->with('status', 'Le profil a été enregistré en brouillon.');
    }

    public function show(Request $request, PrescriptionProfile $profile): Response
    {
        $this->authorize('view', $profile);
        $profile->load(['sections.items.bolus', 'sections.items.infusion', 'author', 'publisher', 'activities.user', 'organization']);

        return Inertia::render('admin/profiles/show', [
            'profile' => [
                ...$profile->profileSnapshot(),
                'id' => $profile->id, 'version' => $profile->version, 'status' => $profile->status,
                'deletedAt' => $profile->deleted_at?->toIso8601String(),
                'supersededAt' => $profile->superseded_at?->toIso8601String(),
                'publishedAt' => $profile->published_at?->toIso8601String(),
                'author' => $profile->author?->name, 'publisher' => $profile->publisher?->name,
                'isDefault' => $profile->organization->default_prescription_profile_id === $profile->id,
                'sections' => $profile->sections->map(fn ($section): array => [
                    'name' => $section->name,
                    'items' => $section->items->map(fn ($item): array => [
                        'type' => $item->bolus_id !== null ? 'bolus' : 'infusion',
                        'recipe_id' => $item->bolus_id ?? $item->infusion_drug_id,
                        'name' => optional($item->bolus ?? $item->infusion)->name ?? 'Recette indisponible',
                        'status' => ($item->bolus ?? $item->infusion)?->status,
                        'deletedAt' => ($item->bolus ?? $item->infusion)?->deleted_at?->toIso8601String(),
                    ])->values(),
                ])->values(),
                'activities' => $profile->activities->map(fn ($activity): array => [
                    'id' => $activity->id, 'action' => $activity->action, 'changes' => $activity->changes,
                    'date' => $activity->created_at->toIso8601String(), 'author' => optional($activity->user)->name ?? 'Système',
                ])->values(),
                'versions' => PrescriptionProfile::withTrashed()->where('profile_id', $profile->profile_id)
                    ->with(['author', 'publisher'])->orderByDesc('version')->get()->map(fn ($version): array => [
                        'id' => $version->id, 'version' => $version->version, 'status' => $version->status,
                        'author' => $version->author?->name, 'publisher' => $version->publisher?->name,
                        'publishedAt' => $version->published_at?->toIso8601String(),
                        'deletedAt' => $version->deleted_at?->toIso8601String(),
                    ])->values(),
            ],
            'pendingDraftId' => PrescriptionProfile::where('profile_id', $profile->profile_id)
                ->where('status', 'draft')->where('id', '!=', $profile->id)->value('id'),
        ]);
    }

    public function update(PrescriptionProfileRequest $request, PrescriptionProfile $profile): RedirectResponse
    {
        DB::transaction(function () use ($request, $profile): void {
            $locked = $this->lockProfile($profile, 'update');
            $locked->update(['name' => $request->validated('name')]);
            $this->saveSections($locked, $request->validated('sections'));
            $this->recordActivity($locked, $request, 'updated');
        });

        return redirect()->route('admin.profiles.show', $profile)->with('status', 'Le brouillon a été mis à jour.');
    }

    public function revise(Request $request, PrescriptionProfile $profile): RedirectResponse
    {
        $this->authorize('revise', $profile);
        $draft = DB::transaction(function () use ($request, $profile): PrescriptionProfile {
            $source = $this->lockProfile($profile, 'revise');
            abort_if(PrescriptionProfile::where('profile_id', $source->profile_id)
                ->whereIn('status', ['draft', 'unpublished'])->exists(), 409, 'Une nouvelle version est déjà en brouillon.');
            $draft = PrescriptionProfile::create([
                'organization_id' => $source->organization_id, 'profile_id' => $source->profile_id,
                'version' => PrescriptionProfile::withTrashed()->where('profile_id', $source->profile_id)->max('version') + 1,
                'name' => $source->name, 'status' => 'draft', 'created_by' => $request->user()->id,
                'supersedes_id' => $source->id,
            ]);
            $this->saveSections($draft, $source->profileSnapshot()['sections']);
            $this->recordActivity($draft, $request, 'revision_created');

            return $draft;
        });

        return redirect()->route('admin.profiles.edit', $draft)->with('status', 'La nouvelle version est en brouillon. La version publiée reste active.');
    }

    public function publish(Request $request, PrescriptionProfile $profile): RedirectResponse
    {
        $this->authorize('update', $profile);
        DB::transaction(function () use ($request, $profile): void {
            $locked = $this->lockProfile($profile, 'update');
            $locked->load('sections.items');
            foreach ($locked->sections as $section) {
                foreach ($section->items as $item) {
                    $model = $item->bolus_id !== null ? Bolus::class : InfusionDrug::class;
                    $recipe = $model::whereKey($item->bolus_id ?? $item->infusion_drug_id)->lockForUpdate()->first();
                    if ($recipe === null || $recipe->status !== 'published' || $recipe->organization_id !== $locked->organization_id) {
                        throw ValidationException::withMessages(['recipes' => 'Toutes les recettes doivent être publiées et non supprimées avant de publier ce profil.']);
                    }
                }
            }
            if ($locked->supersedes_id !== null) {
                $source = PrescriptionProfile::withTrashed()->lockForUpdate()->findOrFail($locked->supersedes_id);
                abort_if($source->superseded_at !== null, 409, 'La version source a déjà été remplacée.');
                $source->update(['superseded_at' => now()]);
                $this->recordActivity($source, $request, 'superseded');
                $defaultTransferred = Organization::whereKey($locked->organization_id)->where('default_prescription_profile_id', $source->id)
                    ->update(['default_prescription_profile_id' => $locked->id]);
                if ($defaultTransferred > 0) {
                    $this->recordActivity($source, $request, 'default_removed');
                    $this->recordActivity($locked, $request, 'default_selected');
                }
            }
            $locked->update(['status' => 'published', 'published_at' => now(), 'published_by' => $request->user()->id]);
            $this->recordActivity($locked, $request, 'published');
        });

        return redirect()->route('admin.profiles.show', $profile)->with('status', 'Le profil a été publié.');
    }

    public function unpublish(Request $request, PrescriptionProfile $profile): RedirectResponse
    {
        $this->authorize('revise', $profile);
        DB::transaction(function () use ($request, $profile): void {
            $locked = $this->lockProfile($profile, 'revise');
            $this->clearDefault($locked, $request);
            $locked->update(['status' => 'unpublished']);
            $this->recordActivity($locked, $request, 'unpublished');
        });

        return redirect()->route('admin.profiles.show', $profile)->with('status', 'Le profil a été dépublié.');
    }

    public function destroy(Request $request, PrescriptionProfile $profile): RedirectResponse
    {
        $this->authorize('delete', $profile);
        DB::transaction(function () use ($request, $profile): void {
            $locked = $this->lockProfile($profile, 'delete');
            $this->clearDefault($locked, $request);
            if ($locked->status === 'published') {
                $locked->update(['status' => 'unpublished']);
                $this->recordActivity($locked, $request, 'unpublished');
            }
            $locked->delete();
            $this->recordActivity($locked, $request, 'deleted');
        });

        return redirect()->route('admin.profiles.index')->with('status', 'Le profil a été supprimé. Son historique est conservé.');
    }

    public function restore(Request $request, int $profile): RedirectResponse
    {
        $trashed = PrescriptionProfile::withTrashed()->findOrFail($profile);
        $this->authorize('restore', $trashed);
        DB::transaction(function () use ($request, $trashed): void {
            $locked = $this->lockProfile($trashed, 'restore', true);
            abort_unless($locked->trashed(), 404);
            abort_if($locked->superseded_at !== null || PrescriptionProfile::withTrashed()
                ->where('profile_id', $locked->profile_id)->where('version', '>', $locked->version)->exists(), 409,
                'Une version ultérieure existe. Restaurez ou modifiez cette version.');
            $locked->restore();
            $this->recordActivity($locked, $request, 'restored');
        });

        return redirect()->route('admin.profiles.show', $profile)->with('status', 'Le profil a été restauré.');
    }

    private function clearDefault(PrescriptionProfile $profile, Request $request): void
    {
        if ($profile->organization->default_prescription_profile_id === $profile->id) {
            $profile->organization->update(['default_prescription_profile_id' => null]);
            $this->recordActivity($profile, $request, 'default_removed');
        }
    }

    public function setDefault(Request $request, PrescriptionProfile $profile): RedirectResponse
    {
        $this->authorize('revise', $profile);
        DB::transaction(function () use ($request, $profile): void {
            $locked = $this->lockProfile($profile, 'revise');
            $organization = $locked->organization;
            if ($organization->default_prescription_profile_id !== null && $organization->default_prescription_profile_id !== $locked->id) {
                $previous = PrescriptionProfile::findOrFail($organization->default_prescription_profile_id);
                $this->recordActivity($previous, $request, 'default_removed');
            }
            $organization->update(['default_prescription_profile_id' => $locked->id]);
            $this->recordActivity($locked, $request, 'default_selected');
        });

        return redirect()->route('admin.profiles.show', $profile)->with('status', 'Le profil par défaut a été changé.');
    }

    public function edit(PrescriptionProfile $profile): Response
    {
        $this->authorize('update', $profile);

        return Inertia::render('admin/profiles/form', [
            'profile' => $profile->profileSnapshot(), 'action' => route('admin.profiles.update', $profile),
            'method' => 'put', 'recipes' => $this->recipeChoices($profile->organization_id),
        ]);
    }

    /** @return list<array{type: string, recipe_id: int, name: string, status: string, version: int}> */
    private function recipeChoices(int $organizationId): array
    {
        $choices = [];
        foreach (['bolus' => Bolus::class, 'infusion' => InfusionDrug::class] as $type => $model) {
            foreach ($model::where('organization_id', $organizationId)->orderBy('name')->orderByDesc('version')->orderBy('id')->get() as $recipe) {
                $choices[] = ['type' => $type, 'recipe_id' => $recipe->id, 'name' => $recipe->name, 'status' => $recipe->status, 'version' => $recipe->version];
            }
        }

        return $choices;
    }

    private function lockProfile(PrescriptionProfile $profile, string $ability, bool $withTrashed = false): PrescriptionProfile
    {
        $organization = Organization::whereKey($profile->organization_id)->lockForUpdate()->firstOrFail();
        $locked = PrescriptionProfile::query()->when($withTrashed, fn ($query) => $query->withTrashed())
            ->lockForUpdate()->findOrFail($profile->id);
        $this->authorize($ability, $locked);
        $locked->setRelation('organization', $organization);

        return $locked;
    }

    /** @param list<array{name: string, items: list<array{type: string, recipe_id: int}>}> $sections */
    private function saveSections(PrescriptionProfile $profile, array $sections): void
    {
        foreach ($sections as $sectionIndex => $section) {
            foreach ($section['items'] as $itemIndex => $item) {
                $model = $item['type'] === 'bolus' ? Bolus::class : InfusionDrug::class;
                $recipe = $model::whereKey($item['recipe_id'])->lockForUpdate()->first();
                if ($recipe === null || $recipe->organization_id !== $profile->organization_id) {
                    throw ValidationException::withMessages(["sections.{$sectionIndex}.items.{$itemIndex}.recipe_id" => 'Choisissez une recette non supprimée de votre centre.']);
                }
            }
        }
        $profile->sections()->delete();
        foreach ($sections as $position => $section) {
            $savedSection = $profile->sections()->create(['name' => $section['name'], 'position' => $position]);
            foreach ($section['items'] as $itemPosition => $item) {
                $savedSection->items()->create([
                    'position' => $itemPosition,
                    'bolus_id' => $item['type'] === 'bolus' ? $item['recipe_id'] : null,
                    'infusion_drug_id' => $item['type'] === 'infusion' ? $item['recipe_id'] : null,
                ]);
            }
        }
        $profile->unsetRelation('sections');
    }

    private function recordActivity(PrescriptionProfile $profile, Request $request, string $action): void
    {
        $profile->activities()->create([
            'user_id' => $request->user()->id, 'action' => $action,
            'changes' => ['version' => $profile->version, 'status' => $profile->status, ...$profile->profileSnapshot()],
        ]);
    }
}
