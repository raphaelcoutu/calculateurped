<?php

use App\Models\Organization;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('authenticates a user with the standard email and password flow', function (): void {
    $organization = Organization::factory()->create();
    $administrator = User::factory()->for($organization)->create([
        'password' => 'motdepasse-valide',
    ]);

    $this->post(route('login.store'), [
        'email' => $administrator->email,
        'password' => 'motdepasse-valide',
    ])->assertRedirect(route('organization.profile.edit'));

    $this->assertAuthenticatedAs($administrator);
});

it('lets a superuser create an organization, invite an administrator, and accept the password link', function (): void {
    Storage::fake('public');
    Notification::fake();
    $superuser = User::factory()->create(['is_superuser' => true]);

    $this->actingAs($superuser)->post(route('admin.organizations.store'), [
        'name' => 'Centre hospitalier du Nord',
        'logo' => UploadedFile::fake()->image('logo.png'),
    ])->assertRedirect();

    $organization = Organization::query()->where('name', 'Centre hospitalier du Nord')->firstOrFail();
    expect($organization->logo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($organization->logo_path);

    $this->post(route('admin.organizations.administrators.store', $organization), [
        'name' => 'Camille Roy',
        'email' => 'camille@example.test',
    ])->assertRedirect();

    $administrator = User::query()->where('email', 'camille@example.test')->firstOrFail();
    $this->assertDatabaseHas('users', [
        'id' => $administrator->id,
        'organization_id' => $organization->id,
        'is_superuser' => false,
    ]);

    $token = null;
    Notification::assertSentTo($administrator, SetPasswordNotification::class, function (SetPasswordNotification $notification) use (&$token): bool {
        $token = $notification->token;

        return true;
    });

    $this->post(route('logout'));
    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $administrator->email,
        'password' => 'motdepasse-configure',
        'password_confirmation' => 'motdepasse-configure',
    ])->assertRedirect(route('login'));

    $this->post(route('login.store'), [
        'email' => $administrator->email,
        'password' => 'motdepasse-configure',
    ])->assertRedirect(route('organization.profile.edit'));
});

it('lets an administrator update their own center but hides other centers', function (): void {
    Storage::fake('public');
    $ownOrganization = Organization::factory()->create(['name' => 'Centre initial']);
    $otherOrganization = Organization::factory()->create(['name' => 'Autre centre']);
    $administrator = User::factory()->for($ownOrganization)->create();

    $this->actingAs($administrator)->put(route('organization.profile.update'), [
        'name' => 'Centre mis à jour',
        'logo' => UploadedFile::fake()->image('nouveau-logo.png'),
    ])->assertRedirect();

    $ownOrganization->refresh();
    expect($ownOrganization->name)->toBe('Centre mis à jour');
    Storage::disk('public')->assertExists($ownOrganization->logo_path);

    $this->put(route('admin.organizations.update', $otherOrganization), [
        'name' => 'Nom interdit',
    ])->assertNotFound();

    expect($otherOrganization->fresh()->name)->toBe('Autre centre');
});

it('rejects administrator access to superuser organization creation', function (): void {
    $organization = Organization::factory()->create();
    $administrator = User::factory()->for($organization)->create();

    $this->actingAs($administrator)
        ->post(route('admin.organizations.store'), ['name' => 'Centre interdit'])
        ->assertForbidden();

    $this->assertDatabaseMissing('organizations', ['name' => 'Centre interdit']);
});

it('requires a logo when a superuser creates an organization', function (): void {
    $superuser = User::factory()->create(['is_superuser' => true]);

    $this->actingAs($superuser)
        ->from(route('admin.organizations.create'))
        ->post(route('admin.organizations.store'), ['name' => 'Centre sans logo'])
        ->assertRedirect(route('admin.organizations.create'))
        ->assertSessionHasErrors(['logo']);

    $this->assertDatabaseMissing('organizations', ['name' => 'Centre sans logo']);
});
