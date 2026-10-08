<?php

namespace App\Policies;

use App\Models\Bolus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BolusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperuser() || $user->organization_id !== null;
    }

    public function view(User $user, Bolus $bolus): bool|Response
    {
        if ($bolus->trashed()) {
            return $user->organization_id === $bolus->organization_id
                ? true
                : Response::denyAsNotFound();
        }

        return $bolus->status === 'published'
            || $user->organization_id === $bolus->organization_id
            ? true
            : Response::denyAsNotFound();
    }

    public function create(User $user): bool
    {
        return ! $user->isSuperuser() && $user->organization_id !== null;
    }

    public function update(User $user, Bolus $bolus): bool|Response
    {
        return ! $user->isSuperuser()
            && $user->organization_id === $bolus->organization_id
            && $bolus->status === 'draft'
            ? true
            : Response::denyAsNotFound();
    }

    public function delete(User $user, Bolus $bolus): bool
    {
        return ! $user->isSuperuser() && $user->organization_id === $bolus->organization_id;
    }

    public function restore(User $user, Bolus $bolus): bool
    {
        return ! $user->isSuperuser() && $user->organization_id === $bolus->organization_id;
    }

    public function forceDelete(User $user, Bolus $bolus): bool
    {
        return false;
    }

    public function revise(User $user, Bolus $bolus): bool|Response
    {
        return ! $user->isSuperuser()
            && $user->organization_id === $bolus->organization_id
            && $bolus->status === 'published'
            && $bolus->superseded_at === null
            ? true
            : Response::denyAsNotFound();
    }

    public function publish(User $user, Bolus $bolus): bool|Response
    {
        return ! $user->isSuperuser()
            && $user->organization_id === $bolus->organization_id
            && $bolus->status === 'draft'
            ? true
            : Response::denyAsNotFound();
    }

    public function copy(User $user, Bolus $bolus): bool|Response
    {
        return $user->organization_id !== null
            && $bolus->status === 'published'
            && $bolus->superseded_at === null
            && ! $bolus->trashed()
            ? true
            : Response::denyAsNotFound();
    }
}
