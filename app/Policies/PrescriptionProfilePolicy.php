<?php

namespace App\Policies;

use App\Models\PrescriptionProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PrescriptionProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return ! $user->isSuperuser() && $user->organization_id !== null;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function view(User $user, PrescriptionProfile $profile): bool|Response
    {
        return $this->viewAny($user) && $user->organization_id === $profile->organization_id
            ? true : Response::denyAsNotFound();
    }

    public function update(User $user, PrescriptionProfile $profile): bool|Response
    {
        return $this->view($user, $profile) === true && ! $profile->trashed()
            && in_array($profile->status, ['draft', 'unpublished'], true) && $profile->superseded_at === null
            ? true : Response::denyAsNotFound();
    }

    public function revise(User $user, PrescriptionProfile $profile): bool|Response
    {
        return $this->view($user, $profile) === true && ! $profile->trashed()
            && $profile->status === 'published' && $profile->superseded_at === null
            ? true : Response::denyAsNotFound();
    }

    public function delete(User $user, PrescriptionProfile $profile): bool|Response
    {
        return $this->view($user, $profile);
    }

    public function restore(User $user, PrescriptionProfile $profile): bool|Response
    {
        return $this->view($user, $profile);
    }
}
