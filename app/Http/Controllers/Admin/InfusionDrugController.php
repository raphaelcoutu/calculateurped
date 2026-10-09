<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InfusionDrugRequest;
use App\Models\InfusionDrug;
use App\Models\PrescriptionItem;
use App\Models\PrescriptionProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class InfusionDrugController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', InfusionDrug::class);
        $search = $request->string('search')->trim()->toString();

        $infusions = InfusionDrug::query()
            ->where('organization_id', $request->user()->organization_id)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereLike('name', "%{$search}%")
                        ->orWhereLike('brand_name', "%{$search}%");
                });
            })
            ->when(! $request->boolean('deleted'), fn (Builder $query): Builder => $query->whereNull('superseded_at'))
            ->addSelect(['has_later_version' => DB::table('infusion_drugs as later_versions')
                ->select('later_versions.id')
                ->whereColumn('later_versions.recipe_id', 'infusion_drugs.recipe_id')
                ->whereColumn('later_versions.version', '>', 'infusion_drugs.version')
                ->where(fn ($query) => $query->where('infusion_drugs.status', 'draft')->orWhere('later_versions.status', 'published'))
                ->limit(1)])
            ->addSelect(['pending_draft_id' => DB::table('infusion_drugs as pending_drafts')
                ->select('pending_drafts.id')
                ->whereColumn('pending_drafts.recipe_id', 'infusion_drugs.recipe_id')
                ->where('pending_drafts.status', 'draft')
                ->whereNull('pending_drafts.deleted_at')
                ->orderByDesc('pending_drafts.version')
                ->limit(1)])
            ->when($request->boolean('deleted'), fn (Builder $query): Builder => $query->onlyTrashed())
            ->with(['author', 'publisher'])
            ->orderBy('name')
            ->orderByDesc('version')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString()
            ->through(function (InfusionDrug $infusion): array {
                $pendingDraftId = $infusion->getAttribute('pending_draft_id');

                return [
                    ...$this->listItem($infusion),
                    'pendingDraftId' => $infusion->status === 'published'
                    && $infusion->superseded_at === null
                    && $pendingDraftId !== null
                        ? (int) $pendingDraftId
                        : null,
                    'canRestore' => $infusion->trashed()
                        && $infusion->superseded_at === null
                        && $infusion->getAttribute('has_later_version') === null,
                ];
            });

        return Inertia::render('admin/infusions/index', [
            'infusions' => $infusions,
            'search' => $search,
            'showDeleted' => $request->boolean('deleted'),
        ]);
    }

    public function catalog(Request $request): Response
    {
        $this->authorize('viewAny', InfusionDrug::class);
        $organizationId = $request->user()->organization_id;

        return Inertia::render('admin/infusions/catalog', [
            'infusions' => InfusionDrug::published()
                ->with(['organization', 'author', 'publisher'])
                ->orderBy('name')
                ->paginate(20)
                ->through(fn (InfusionDrug $infusion): array => [
                    ...$this->listItem($infusion),
                    'organization' => $infusion->organization->name,
                    'canCopy' => $organizationId !== null && $organizationId !== $infusion->organization_id,
                ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', InfusionDrug::class);

        return Inertia::render('admin/infusions/form', [
            'doseUnits' => InfusionDrug::doseUnits(),
            'infusion' => null,
            'action' => route('admin.infusions.store'),
            'method' => 'post',
        ]);
    }

    public function store(InfusionDrugRequest $request): RedirectResponse
    {
        $this->authorize('create', InfusionDrug::class);

        $infusion = DB::transaction(function () use ($request): InfusionDrug {
            $infusion = InfusionDrug::create([
                ...$request->safe()->except(['preparations', 'dose_unit']),
                'brand_name' => $request->input('brand_name') ?? '',
                'organization_id' => $request->user()->organization_id,
                'recipe_id' => (string) Str::uuid(),
                'version' => 1,
                'type' => 1,
                'order' => 1,
                'status' => 'draft',
                'created_by' => $request->user()->id,
            ]);

            $this->savePreparations($infusion, $request);
            $this->recordActivity($infusion, $request, 'created', $infusion->recipeSnapshot());

            return $infusion;
        });

        return redirect()->route('admin.infusions.show', $infusion)
            ->with('status', 'La fiche de perfusion a été enregistrée en brouillon.');
    }

    public function show(Request $request, InfusionDrug $infusion): Response
    {
        $this->authorize('view', $infusion);

        $canManage = ! $request->user()->isSuperuser()
            && $request->user()->organization_id === $infusion->organization_id;
        $pendingDraftId = $canManage && ! $infusion->trashed() && $infusion->status === 'published' && $infusion->superseded_at === null
            ? InfusionDrug::query()
                ->where('recipe_id', $infusion->recipe_id)
                ->where('status', 'draft')
                ->orderByDesc('version')
                ->value('id')
            : null;
        $infusion->load(['organization', 'author', 'publisher', 'activities.user']);

        $versions = InfusionDrug::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->where('recipe_id', $infusion->recipe_id)
            ->when(! $canManage, fn (Builder $query): Builder => $query->where('status', 'published'))
            ->with(['author', 'publisher'])
            ->orderByDesc('version')
            ->get()
            ->map(fn (InfusionDrug $version): array => [
                'id' => $version->id,
                'version' => $version->version,
                'status' => $version->status,
                'author' => $version->author?->name,
                'publisher' => $version->publisher?->name,
                'publishedAt' => $version->published_at?->toIso8601String(),
                'supersededAt' => $version->superseded_at?->toIso8601String(),
                'deletedAt' => $version->deleted_at?->toIso8601String(),
            ])
            ->values();

        return Inertia::render('admin/infusions/show', [
            'gaps' => $infusion->coverageGaps(),
            'canManage' => $canManage,
            'usingProfiles' => $canManage ? PrescriptionProfile::query()
                ->where('organization_id', $request->user()->organization_id)
                ->whereHas('sections.items', fn (Builder $query): Builder => $query->where('infusion_drug_id', $infusion->id))
                ->orderBy('name')
                ->orderByDesc('version')
                ->orderBy('id')
                ->get()
                ->map(fn (PrescriptionProfile $profile): array => [
                    'id' => $profile->id,
                    'name' => $profile->name,
                    'version' => $profile->version,
                    'status' => $profile->status,
                    'supersededAt' => $profile->superseded_at?->toIso8601String(),
                    'url' => route('admin.profiles.show', $profile),
                ])->values() : [],
            'pendingDraftId' => $pendingDraftId === null ? null : (int) $pendingDraftId,
            'canCopy' => $infusion->superseded_at === null && ! $infusion->trashed()
                && $request->user()->organization_id !== null
                && $infusion->status === 'published'
                && $request->user()->organization_id !== $infusion->organization_id,
            'infusion' => [
                ...$this->listItem($infusion),
                ...$infusion->recipeSnapshot(),
                'organization' => $infusion->organization->name,
                'versions' => $versions,
                'activities' => $infusion->activities->map(fn ($activity): array => [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'changes' => $activity->changes,
                    'date' => $activity->created_at->toIso8601String(),
                    'author' => optional($activity->user)->name ?? 'Système',
                ])->values(),
            ],
        ]);
    }

    public function edit(InfusionDrug $infusion): Response
    {
        $this->authorize('update', $infusion);

        return Inertia::render('admin/infusions/form', [
            'doseUnits' => InfusionDrug::doseUnits(),
            'infusion' => [...$this->listItem($infusion), ...$infusion->recipeSnapshot()],
            'action' => route('admin.infusions.update', $infusion),
            'method' => 'put',
        ]);
    }

    public function update(InfusionDrugRequest $request, InfusionDrug $infusion): RedirectResponse
    {
        $this->authorize('update', $infusion);

        DB::transaction(function () use ($infusion, $request): void {
            $lockedInfusionDrug = InfusionDrug::query()->lockForUpdate()->findOrFail($infusion->id);
            $this->authorize('update', $lockedInfusionDrug);
            $lockedInfusionDrug->update([
                ...$request->safe()->except(['preparations', 'dose_unit']),
                'brand_name' => $request->input('brand_name') ?? '',
            ]);

            $this->savePreparations($lockedInfusionDrug, $request);
            $this->recordActivity($lockedInfusionDrug, $request, 'updated', $lockedInfusionDrug->recipeSnapshot());
        });

        return redirect()->route('admin.infusions.show', $infusion)
            ->with('status', 'Le brouillon a été mis à jour.');
    }

    public function revise(Request $request, InfusionDrug $infusion): RedirectResponse
    {
        $this->authorize('revise', $infusion);

        $draft = DB::transaction(function () use ($infusion, $request): InfusionDrug {
            $source = InfusionDrug::query()->lockForUpdate()->findOrFail($infusion->id);
            abort_unless($source->status === 'published' && $source->superseded_at === null, 409);
            abort_if(
                InfusionDrug::query()->where('recipe_id', $source->recipe_id)->where('status', 'draft')->exists(),
                409,
                'Une nouvelle version est déjà en brouillon.'
            );

            $version = InfusionDrug::withTrashed()->where('recipe_id', $source->recipe_id)->max('version') + 1;
            $draft = InfusionDrug::create([
                ...$source->recipeAttributes(),
                'organization_id' => $source->organization_id,
                'recipe_id' => $source->recipe_id,
                'version' => $version,
                'status' => 'draft',
                'created_by' => $request->user()->id,
                'supersedes_id' => $source->id,
            ]);

            $this->copyPreparations($source, $draft);
            $this->recordActivity($draft, $request, 'revision_created', [
                'source_version' => $source->version,
                ...$draft->recipeSnapshot(),
            ]);

            return $draft;
        });

        return redirect()->route('admin.infusions.edit', $draft)
            ->with('status', 'Une nouvelle version brouillon a été créée. La version publiée reste active.');
    }

    public function publish(Request $request, InfusionDrug $infusion): RedirectResponse
    {
        $this->authorize('publish', $infusion);

        DB::transaction(function () use ($infusion, $request): void {
            $lockedInfusionDrug = InfusionDrug::query()->lockForUpdate()->findOrFail($infusion->id);
            $this->authorize('publish', $lockedInfusionDrug);
            abort_unless($lockedInfusionDrug->status === 'draft', 409);

            if ($lockedInfusionDrug->supersedes_id !== null) {
                $sourceVersion = InfusionDrug::query()
                    ->lockForUpdate()
                    ->whereKey($lockedInfusionDrug->supersedes_id)
                    ->whereNull('superseded_at')
                    ->where('status', 'published')
                    ->first();

                abort_if($sourceVersion === null, 409);
                $sourceVersion->update(['superseded_at' => now()]);
            }

            $lockedInfusionDrug->update([
                'status' => 'published',
                'published_at' => now(),
                'published_by' => $request->user()->id,
            ]);

            $this->recordActivity($lockedInfusionDrug, $request, 'published', [
                'version' => $lockedInfusionDrug->version,
                'published_at' => $lockedInfusionDrug->published_at->toIso8601String(),
                ...$lockedInfusionDrug->recipeSnapshot(),
            ]);
        });

        return redirect()->route('admin.infusions.show', $infusion)
            ->with('status', 'La fiche de perfusion a été publiée dans le catalogue.');
    }

    public function copy(Request $request, InfusionDrug $infusion): RedirectResponse
    {
        $this->authorize('copy', $infusion);

        $copy = DB::transaction(function () use ($infusion, $request): InfusionDrug {
            $source = InfusionDrug::query()->lockForUpdate()->findOrFail($infusion->id);
            $this->authorize('copy', $source);
            $infusion = $source;
            $copy = InfusionDrug::create([
                ...$infusion->recipeAttributes(),
                'organization_id' => $request->user()->organization_id,
                'recipe_id' => (string) Str::uuid(),
                'version' => 1,
                'status' => 'draft',
                'created_by' => $request->user()->id,
            ]);

            $this->copyPreparations($infusion, $copy);
            $this->recordActivity($copy, $request, 'copied', [
                'source_organization' => $infusion->organization()->value('name'),
                'source_version' => $infusion->version,
                ...$copy->recipeSnapshot(),
            ]);

            return $copy;
        });

        return redirect()->route('admin.infusions.edit', $copy)
            ->with('status', 'Une copie indépendante a été créée dans vos brouillons.');
    }

    public function unpublish(Request $request, InfusionDrug $infusion): RedirectResponse
    {
        $this->authorize('revise', $infusion);
        DB::transaction(function () use ($request, $infusion): void {
            $locked = InfusionDrug::query()->lockForUpdate()->findOrFail($infusion->id);
            $this->authorize('revise', $locked);
            PrescriptionItem::assertRecipeCanBeWithdrawn('infusion_drug_id', $locked->id);
            abort_if(InfusionDrug::where('recipe_id', $locked->recipe_id)->where('status', 'draft')->exists(), 409,
                'Une nouvelle version est déjà en brouillon.');
            $locked->update(['status' => 'draft']);
            $this->recordActivity($locked, $request, 'unpublished', ['version' => $locked->version]);
        });

        return redirect()->route('admin.infusions.show', $infusion)
            ->with('status', 'La recette a été dépubliée et peut être modifiée.');
    }

    public function destroy(Request $request, InfusionDrug $infusion): RedirectResponse
    {
        $this->authorize('delete', $infusion);

        DB::transaction(function () use ($infusion, $request): void {
            $lockedInfusionDrug = InfusionDrug::query()->lockForUpdate()->findOrFail($infusion->id);
            $this->authorize('delete', $lockedInfusionDrug);
            PrescriptionItem::assertRecipeCanBeWithdrawn('infusion_drug_id', $lockedInfusionDrug->id);
            $lockedInfusionDrug->delete();
            $this->recordActivity($lockedInfusionDrug, $request, 'deleted', ['version' => $lockedInfusionDrug->version]);
        });

        return redirect()->route('admin.infusions.index')
            ->with('status', 'La fiche de perfusion a été déplacée dans les éléments supprimés.');
    }

    public function restore(Request $request, int $infusion): RedirectResponse
    {
        $trashedInfusionDrug = InfusionDrug::withTrashed()->findOrFail($infusion);
        $this->authorize('restore', $trashedInfusionDrug);

        abort_unless($trashedInfusionDrug->trashed(), 404);
        DB::transaction(function () use ($trashedInfusionDrug, $request): void {
            $lockedInfusionDrug = InfusionDrug::withTrashed()->lockForUpdate()->findOrFail($trashedInfusionDrug->id);
            $this->authorize('restore', $lockedInfusionDrug);

            abort_unless($lockedInfusionDrug->trashed(), 404);
            abort_if(
                $lockedInfusionDrug->superseded_at !== null
                    || InfusionDrug::withTrashed()
                        ->where('recipe_id', $lockedInfusionDrug->recipe_id)
                        ->where('version', '>', $lockedInfusionDrug->version)
                        ->when($lockedInfusionDrug->status === 'published', fn (Builder $query): Builder => $query->where('status', 'published'))
                        ->exists(),
                409,
                'Cette version a été remplacée par une publication ou un brouillon ultérieur et ne peut plus être restaurée.'
            );

            $lockedInfusionDrug->restore();
            $this->recordActivity($lockedInfusionDrug, $request, 'restored', ['version' => $lockedInfusionDrug->version]);
        });

        return redirect()->route('admin.infusions.show', $trashedInfusionDrug)
            ->with('status', 'La fiche de perfusion a été restaurée.');
    }

    private function copyPreparations(InfusionDrug $source, InfusionDrug $destination): void
    {
        foreach ($source->concentrations as $preparation) {
            $destination->concentrations()->create($preparation->preparationAttributes());
        }
    }

    /** @return array<string, mixed> */
    private function listItem(InfusionDrug $infusion): array
    {
        return [
            'id' => $infusion->id,
            'name' => $infusion->name,
            'brandName' => $infusion->brand_name,
            'status' => $infusion->status,
            'version' => $infusion->version,
            'publishedAt' => $infusion->published_at?->toIso8601String(),
            'supersededAt' => $infusion->superseded_at?->toIso8601String(),
            'deletedAt' => $infusion->deleted_at?->toIso8601String(),
            'author' => $infusion->author?->name,
            'publisher' => $infusion->publisher?->name,
        ];
    }

    private function savePreparations(InfusionDrug $infusion, InfusionDrugRequest $request): void
    {
        $infusion->concentrations()->delete();
        foreach ($request->validated('preparations') as $position => $preparation) {
            $infusion->concentrations()->create([
                ...$preparation, 'position' => $position,
                'instructions' => $preparation['instructions'] ?? '',
                'max_weight' => $preparation['max_weight'] ?? null,
            ]);
        }
        $infusion->unsetRelation('concentrations');
    }

    /** @param array<string, mixed> $changes */
    private function recordActivity(InfusionDrug $infusion, Request $request, string $action, array $changes): void
    {
        $infusion->activities()->create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'changes' => $changes,
        ]);
    }
}
