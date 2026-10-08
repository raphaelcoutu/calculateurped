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
        'name' => 'Organisation hospitalière du Nord',
        'logo' => UploadedFile::fake()->image('logo.png'),
    ])->assertRedirect();

    $organization = Organization::query()->where('name', 'Organisation hospitalière du Nord')->firstOrFail();
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

it('lets an administrator update their own organization but hides other organizations', function (): void {
    Storage::fake('public');
    $ownOrganization = Organization::factory()->create(['name' => 'Organisation initiale']);
    $otherOrganization = Organization::factory()->create(['name' => 'Autre organisation']);
    $administrator = User::factory()->for($ownOrganization)->create();

    $this->actingAs($administrator)->put(route('organization.profile.update'), [
        'name' => 'Organisation mise à jour',
        'logo' => UploadedFile::fake()->image('nouveau-logo.png'),
    ])->assertRedirect();

    $ownOrganization->refresh();
    expect($ownOrganization->name)->toBe('Organisation mise à jour');
    Storage::disk('public')->assertExists($ownOrganization->logo_path);

    $this->put(route('admin.organizations.update', $otherOrganization), [
        'name' => 'Nom interdit',
    ])->assertNotFound();

    expect($otherOrganization->fresh()->name)->toBe('Autre organisation');
});

it('rejects administrator access to superuser organization creation', function (): void {
    $organization = Organization::factory()->create();
    $administrator = User::factory()->for($organization)->create();

    $this->actingAs($administrator)
        ->post(route('admin.organizations.store'), ['name' => 'Organisation interdite'])
        ->assertForbidden();

    $this->assertDatabaseMissing('organizations', ['name' => 'Organisation interdite']);
});

it('requires a logo when a superuser creates an organization', function (): void {
    $superuser = User::factory()->create(['is_superuser' => true]);

    $this->actingAs($superuser)
        ->from(route('admin.organizations.create'))
        ->post(route('admin.organizations.store'), ['name' => 'Organisation sans logo'])
        ->assertRedirect(route('admin.organizations.create'))
        ->assertInvalid(['logo' => 'Le logo de l’organisation est obligatoire.']);

    $this->assertDatabaseMissing('organizations', ['name' => 'Organisation sans logo']);
});

dataset('invalid organization input', [
    'name is required' => [[], 'name', 'Le nom de l’organisation est obligatoire.'],
    'name is too long' => [[
        'name' => str_repeat('a', 256),
        'logo' => fn () => UploadedFile::fake()->image('logo.png'),
    ], 'name', 'Le nom de l’organisation ne peut pas dépasser 255 caractères.'],
    'logo is not an image' => [[
        'name' => 'Organisation valide',
        'logo' => fn () => UploadedFile::fake()->create('logo.txt', 1, 'text/plain'),
    ], 'logo', 'Le logo doit être une image.'],
    'logo has an unsupported format' => [[
        'name' => 'Organisation valide',
        'logo' => fn () => UploadedFile::fake()->image('logo.gif'),
    ], 'logo', 'Le logo doit être au format JPG, PNG ou WebP.'],
    'logo is too large' => [[
        'name' => 'Organisation valide',
        'logo' => fn () => UploadedFile::fake()->image('logo.png')->size(2049),
    ], 'logo', 'Le logo ne peut pas dépasser 2 Mo.'],
]);

it('shows the validation message when organization input is invalid', function (array $input, string $field, string $message): void {
    $superuser = User::factory()->create(['is_superuser' => true]);
    $input = collect($input)->map(fn ($value) => $value instanceof Closure ? $value() : $value)->all();

    $this->actingAs($superuser)
        ->from(route('admin.organizations.create'))
        ->post(route('admin.organizations.store'), $input)
        ->assertRedirect(route('admin.organizations.create'))
        ->assertInvalid([$field => $message]);
})->with('invalid organization input');
