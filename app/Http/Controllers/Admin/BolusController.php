<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BolusRequest;
use App\Models\Bolus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BolusController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Bolus::class);
        $search = $request->string('search')->trim()->toString();

        $boluses = Bolus::query()
            ->where('organization_id', $request->user()->organization_id)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereLike('name', "%{$search}%")
                        ->orWhereLike('brand_name', "%{$search}%");
                });
            })
            ->addSelect(['pending_draft_id' => DB::table('boluses as pending_drafts')
                ->select('pending_drafts.id')
                ->whereColumn('pending_drafts.recipe_id', 'boluses.recipe_id')
                ->where('pending_drafts.status', 'draft')
                ->whereNull('pending_drafts.deleted_at')
                ->orderByDesc('pending_drafts.version')
                ->limit(1)])
            ->when($request->boolean('deleted'), fn (Builder $query): Builder => $query->onlyTrashed())
            ->with(['author', 'publisher'])
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString()
            ->through(function (Bolus $bolus): array {
                $pendingDraftId = $bolus->getAttribute('pending_draft_id');

                return [
                    ...$this->listItem($bolus),
                    'pendingDraftId' => $bolus->status === 'published'
                    && $bolus->superseded_at === null
                    && $pendingDraftId !== null
                        ? (int) $pendingDraftId
                        : null,
                ];
            });

        return Inertia::render('admin/boluses/index', [
            'boluses' => $boluses,
            'search' => $search,
            'showDeleted' => $request->boolean('deleted'),
        ]);
    }

    public function catalog(Request $request): Response
    {
        $this->authorize('viewAny', Bolus::class);

        return Inertia::render('admin/boluses/catalog', [
            'canCopy' => $request->user()->organization_id !== null,
            'boluses' => Bolus::published()
                ->with('organization')
                ->orderBy('name')
                ->paginate(20)
                ->through(fn (Bolus $bolus): array => [
                    ...$this->listItem($bolus),
                    'organization' => $bolus->organization->name,
                ]),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Bolus::class);

        return Inertia::render('admin/boluses/form', [
            'bolus' => null,
            'action' => route('admin.boluses.store'),
            'method' => 'post',
        ]);
    }

    public function store(BolusRequest $request): RedirectResponse
    {
        $this->authorize('create', Bolus::class);

        $bolus = DB::transaction(function () use ($request): Bolus {
            $bolus = Bolus::create([
                ...$request->validated(),
                'organization_id' => $request->user()->organization_id,
                'recipe_id' => (string) Str::uuid(),
                'version' => 1,
                'status' => 'draft',
                'created_by' => $request->user()->id,
                'asterisk' => $request->boolean('asterisk'),
            ]);

            $this->recordActivity($bolus, $request, 'created', $bolus->recipeAttributes());

            return $bolus;
        });

        return redirect()->route('admin.boluses.show', $bolus)
            ->with('status', 'Le bolus a été enregistré en brouillon.');
    }

    public function show(Request $request, Bolus $bolus): Response
    {
        $this->authorize('view', $bolus);

        $canManage = ! $request->user()->isSuperuser()
            && $request->user()->organization_id === $bolus->organization_id;
        $pendingDraftId = $canManage && $bolus->status === 'published' && $bolus->superseded_at === null
            ? Bolus::query()
                ->where('recipe_id', $bolus->recipe_id)
                ->where('status', 'draft')
                ->orderByDesc('version')
                ->value('id')
            : null;
        $bolus->load(['organization', 'author', 'publisher', 'activities.user']);

        $versions = Bolus::query()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->where('recipe_id', $bolus->recipe_id)
            ->when(! $canManage, fn (Builder $query): Builder => $query->where('status', 'published'))
            ->with(['author', 'publisher'])
            ->orderByDesc('version')
            ->get()
            ->map(fn (Bolus $version): array => [
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

        return Inertia::render('admin/boluses/show', [
            'canManage' => $canManage,
            'pendingDraftId' => $pendingDraftId === null ? null : (int) $pendingDraftId,
            'canCopy' => $request->user()->organization_id !== null
                && $bolus->status === 'published'
                && $request->user()->organization_id !== $bolus->organization_id,
            'bolus' => [
                ...$this->listItem($bolus),
                ...$bolus->recipeAttributes(),
                'organization' => $bolus->organization->name,
                'versions' => $versions,
                'activities' => $bolus->activities->map(fn ($activity): array => [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'changes' => $activity->changes,
                    'date' => $activity->created_at->toIso8601String(),
                    'author' => optional($activity->user)->name ?? 'Système',
                ])->values(),
            ],
        ]);
    }

    public function edit(Bolus $bolus): Response
    {
        $this->authorize('update', $bolus);

        return Inertia::render('admin/boluses/form', [
            'bolus' => [...$this->listItem($bolus), ...$bolus->recipeAttributes()],
            'action' => route('admin.boluses.update', $bolus),
            'method' => 'put',
        ]);
    }

    public function update(BolusRequest $request, Bolus $bolus): RedirectResponse
    {
        $this->authorize('update', $bolus);

        DB::transaction(function () use ($bolus, $request): void {
            $lockedBolus = Bolus::query()->lockForUpdate()->findOrFail($bolus->id);
            $this->authorize('update', $lockedBolus);
            $lockedBolus->update([
                ...$request->validated(),
                'asterisk' => $request->boolean('asterisk'),
            ]);

            $this->recordActivity($lockedBolus, $request, 'updated', $lockedBolus->recipeAttributes());
        });

        return redirect()->route('admin.boluses.show', $bolus)
            ->with('status', 'Le brouillon a été mis à jour.');
    }

    public function revise(Request $request, Bolus $bolus): RedirectResponse
    {
        $this->authorize('revise', $bolus);

        $draft = DB::transaction(function () use ($bolus, $request): Bolus {
            $source = Bolus::query()->lockForUpdate()->findOrFail($bolus->id);
            abort_unless($source->status === 'published' && $source->superseded_at === null, 409);
            abort_if(
                Bolus::query()->where('recipe_id', $source->recipe_id)->where('status', 'draft')->exists(),
                409,
                'Une nouvelle version est déjà en brouillon.'
            );

            $version = Bolus::withTrashed()->where('recipe_id', $source->recipe_id)->max('version') + 1;
            $draft = Bolus::create([
                ...$source->recipeAttributes(),
                'organization_id' => $source->organization_id,
                'recipe_id' => $source->recipe_id,
                'version' => $version,
                'status' => 'draft',
                'created_by' => $request->user()->id,
                'supersedes_id' => $source->id,
            ]);

            $this->recordActivity($draft, $request, 'revision_created', [
                'source_version' => $source->version,
                ...$draft->recipeAttributes(),
            ]);

            return $draft;
        });

        return redirect()->route('admin.boluses.edit', $draft)
            ->with('status', 'Une nouvelle version brouillon a été créée. La version publiée reste active.');
    }

    public function publish(Request $request, Bolus $bolus): RedirectResponse
    {
        $this->authorize('publish', $bolus);

        DB::transaction(function () use ($bolus, $request): void {
            $lockedBolus = Bolus::query()->lockForUpdate()->findOrFail($bolus->id);
            abort_unless($lockedBolus->status === 'draft', 409);

            if ($lockedBolus->supersedes_id !== null) {
                $sourceVersion = Bolus::query()
                    ->lockForUpdate()
                    ->whereKey($lockedBolus->supersedes_id)
                    ->whereNull('superseded_at')
                    ->where('status', 'published')
                    ->first();

                abort_if($sourceVersion === null, 409);
                $sourceVersion->update(['superseded_at' => now()]);
            }

            $lockedBolus->update([
                'status' => 'published',
                'published_at' => now(),
                'published_by' => $request->user()->id,
            ]);

            $this->recordActivity($lockedBolus, $request, 'published', [
                'version' => $lockedBolus->version,
                'published_at' => $lockedBolus->published_at->toIso8601String(),
                ...$lockedBolus->recipeAttributes(),
            ]);
        });

        return redirect()->route('admin.boluses.show', $bolus)
            ->with('status', 'Le bolus a été publié dans le catalogue.');
    }

    public function copy(Request $request, Bolus $bolus): RedirectResponse
    {
        $this->authorize('copy', $bolus);

        $copy = DB::transaction(function () use ($bolus, $request): Bolus {
            $copy = Bolus::create([
                ...$bolus->recipeAttributes(),
                'organization_id' => $request->user()->organization_id,
                'recipe_id' => (string) Str::uuid(),
                'version' => 1,
                'status' => 'draft',
                'created_by' => $request->user()->id,
                'copied_from_id' => $bolus->id,
            ]);

            $this->recordActivity($copy, $request, 'copied', [
                'source_organization' => $bolus->organization()->value('name'),
                'source_version' => $bolus->version,
                ...$copy->recipeAttributes(),
            ]);

            return $copy;
        });

        return redirect()->route('admin.boluses.edit', $copy)
            ->with('status', 'Une copie indépendante a été créée dans vos brouillons.');
    }

    public function destroy(Request $request, Bolus $bolus): RedirectResponse
    {
        $this->authorize('delete', $bolus);

        DB::transaction(function () use ($bolus, $request): void {
            $lockedBolus = Bolus::query()->lockForUpdate()->findOrFail($bolus->id);
            $this->authorize('delete', $lockedBolus);
            $lockedBolus->delete();
            $this->recordActivity($lockedBolus, $request, 'deleted', ['version' => $lockedBolus->version]);
        });

        return redirect()->route('admin.boluses.index')
            ->with('status', 'Le bolus a été déplacé dans les éléments supprimés.');
    }

    public function restore(Request $request, int $bolus): RedirectResponse
    {
        $trashedBolus = Bolus::withTrashed()->findOrFail($bolus);
        $this->authorize('restore', $trashedBolus);

        abort_unless($trashedBolus->trashed(), 404);
        DB::transaction(function () use ($trashedBolus, $request): void {
            $trashedBolus->restore();
            $this->recordActivity($trashedBolus, $request, 'restored', ['version' => $trashedBolus->version]);
        });

        return redirect()->route('admin.boluses.show', $trashedBolus)
            ->with('status', 'Le bolus a été restauré.');
    }

    /** @return array<string, mixed> */
    private function listItem(Bolus $bolus): array
    {
        return [
            'id' => $bolus->id,
            'name' => $bolus->name,
            'brandName' => $bolus->brand_name,
            'status' => $bolus->status,
            'version' => $bolus->version,
            'publishedAt' => $bolus->published_at?->toIso8601String(),
            'supersededAt' => $bolus->superseded_at?->toIso8601String(),
            'deletedAt' => $bolus->deleted_at?->toIso8601String(),
            'author' => $bolus->author?->name,
            'publisher' => $bolus->publisher?->name,
        ];
    }

    /** @param array<string, mixed> $changes */
    private function recordActivity(Bolus $bolus, Request $request, string $action, array $changes): void
    {
        $bolus->activities()->create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'changes' => $changes,
        ]);
    }
}
