<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OrganizationAdministratorController extends Controller
{
    public function store(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L’adresse courriel est obligatoire.',
            'email.email' => 'L’adresse courriel doit être valide.',
            'email.unique' => 'Un compte existe déjà avec cette adresse courriel.',
        ]);

        $administrator = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::random(64)),
            'organization_id' => $organization->id,
            'is_superuser' => false,
        ]);

        Password::sendResetLink(['email' => $administrator->email]);

        return back()->with('status', 'Le compte administrateur a été créé et son lien de définition du mot de passe a été envoyé.');
    }
}
