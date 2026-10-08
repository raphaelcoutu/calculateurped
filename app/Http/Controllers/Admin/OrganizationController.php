<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Organization::class);

        return Inertia::render('admin/organizations/index', [
            'organizations' => Organization::query()->withCount('users')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Organization::class);

        return Inertia::render('admin/organizations/form', [
            'organization' => null,
            'action' => route('admin.organizations.store'),
            'method' => 'post',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Le nom de l’organisation est obligatoire.',
            'name.max' => 'Le nom de l’organisation ne peut pas dépasser 255 caractères.',
            'logo.required' => 'Le logo de l’organisation est obligatoire.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être au format JPG, PNG ou WebP.',
            'logo.max' => 'Le logo ne peut pas dépasser 2 Mo.',
        ]);

        $organization = Organization::create([
            'name' => $validated['name'],
            'logo_path' => $request->file('logo')->store('organizations', 'public'),
        ]);

        return redirect()->route('admin.organizations.show', $organization)
            ->with('status', 'L’organisation a été créée. Vous pouvez maintenant inviter ses administrateurs.');
    }

    public function show(Organization $organization): Response
    {
        $this->authorize('view', $organization);

        $organization->load(['users' => fn ($query) => $query->orderBy('email')]);

        return Inertia::render('admin/organizations/show', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'logoUrl' => $organization->logo_path === null ? null : Storage::disk('public')->url($organization->logo_path),
                'users' => $organization->users
                    ->reject(fn ($user): bool => $user->isSuperuser())
                    ->map(fn ($user): array => $user->only(['id', 'name', 'email']))
                    ->values(),
            ],
        ]);
    }

    public function edit(Organization $organization): Response
    {
        $this->authorize('update', $organization);

        return Inertia::render('admin/organizations/form', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'logoUrl' => $organization->logo_path === null ? null : Storage::disk('public')->url($organization->logo_path),
            ],
            'action' => route('admin.organizations.update', $organization),
            'method' => 'put',
        ]);
    }
}
