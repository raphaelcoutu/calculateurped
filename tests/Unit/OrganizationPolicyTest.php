<?php

use App\Models\Organization;
use App\Models\User;
use App\Policies\OrganizationPolicy;

it('grants superusers access to all organization management abilities', function (): void {
    $policy = new OrganizationPolicy;
    $superuser = new User(['is_superuser' => true]);
    $organization = new Organization;

    expect($policy->viewAny($superuser))->toBeTrue()
        ->and($policy->create($superuser))->toBeTrue()
        ->and($policy->view($superuser, $organization))->toBeTrue()
        ->and($policy->update($superuser, $organization))->toBeTrue();
});

it('limits administrators to viewing and updating their own organization', function (): void {
    $policy = new OrganizationPolicy;
    $ownOrganization = new Organization;
    $ownOrganization->id = 1;
    $otherOrganization = new Organization;
    $otherOrganization->id = 2;
    $administrator = new User([
        'organization_id' => $ownOrganization->id,
        'is_superuser' => false,
    ]);

    expect($policy->viewAny($administrator))->toBeFalse()
        ->and($policy->create($administrator))->toBeFalse()
        ->and($policy->view($administrator, $ownOrganization))->toBeTrue()
        ->and($policy->update($administrator, $ownOrganization))->toBeTrue()
        ->and($policy->view($administrator, $otherOrganization)->status())->toBe(404)
        ->and($policy->update($administrator, $otherOrganization)->status())->toBe(404);
});
