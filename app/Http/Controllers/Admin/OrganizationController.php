<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Organization::class);

        return view('admin.organizations.index', [
            'organizations' => Organization::query()->withCount('users')->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Organization::class);

        return view('admin.organizations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Le nom du centre est obligatoire.',
            'name.max' => 'Le nom du centre ne peut pas dépasser 255 caractères.',
            'logo.required' => 'Le logo du centre est obligatoire.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être au format JPG, PNG ou WebP.',
            'logo.max' => 'Le logo ne peut pas dépasser 2 Mo.',
        ]);

        $organization = Organization::create([
            'name' => $validated['name'],
            'logo_path' => $request->file('logo')->store('organizations', 'public'),
        ]);

        return redirect()->route('admin.organizations.show', $organization)
            ->with('status', 'Le centre a été créé. Vous pouvez maintenant inviter ses administrateurs.');
    }

    public function show(Organization $organization): View
    {
        $this->authorize('view', $organization);

        return view('admin.organizations.show', [
            'organization' => $organization->load(['users' => fn ($query) => $query->orderBy('email')]),
        ]);
    }

    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);

        return view('admin.organizations.edit', compact('organization'));
    }
}
