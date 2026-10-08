<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $organization = $request->user()->organization;

        abort_if($organization === null, 404);
        $this->authorize('update', $organization);

        return Inertia::render('organization/profile/edit', [
            'organization' => [
                'name' => $organization->name,
                'logoUrl' => $organization->logo_path === null ? null : Storage::disk('public')->url($organization->logo_path),
            ],
        ]);
    }

    public function update(Request $request, ?Organization $organization = null): RedirectResponse
    {
        $organization ??= $request->user()->organization;

        abort_if($organization === null, 404);
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Le nom de l’organisation est obligatoire.',
            'name.max' => 'Le nom de l’organisation ne peut pas dépasser 255 caractères.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être au format JPG, PNG ou WebP.',
            'logo.max' => 'Le logo ne peut pas dépasser 2 Mo.',
        ]);

        $organization->update(['name' => $validated['name']]);

        if ($request->hasFile('logo')) {
            $oldLogo = $organization->logo_path;
            $organization->update(['logo_path' => $request->file('logo')->store('organizations', 'public')]);

            if ($oldLogo !== null) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        return back()->with('status', 'Les renseignements de l’organisation ont été mis à jour.');
    }
}
