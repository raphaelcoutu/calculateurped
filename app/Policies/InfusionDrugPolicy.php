<?php

namespace App\Policies;

use App\Models\InfusionDrug;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InfusionDrugPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperuser() || $user->organization_id !== null;
    }

    public function view(User $user, InfusionDrug $infusion): bool|Response
    {
        if ($infusion->trashed()) {
            return $user->organization_id === $infusion->organization_id
                ? true
                : Response::denyAsNotFound();
        }

        return $infusion->status === 'published'
            || $user->organization_id === $infusion->organization_id
            ? true
            : Response::denyAsNotFound();
    }

    public function create(User $user): bool
    {
        return ! $user->isSuperuser() && $user->organization_id !== null;
    }

    public function update(User $user, InfusionDrug $infusion): bool|Response
    {
        return ! $user->isSuperuser()
            && $user->organization_id === $infusion->organization_id
            && $infusion->status === 'draft'
            ? true
            : Response::denyAsNotFound();
    }

    public function delete(User $user, InfusionDrug $infusion): bool
    {
        return ! $user->isSuperuser() && $user->organization_id === $infusion->organization_id;
    }

    public function restore(User $user, InfusionDrug $infusion): bool
    {
        return ! $user->isSuperuser() && $user->organization_id === $infusion->organization_id;
    }

    public function forceDelete(User $user, InfusionDrug $infusion): bool
    {
        return false;
    }

    public function revise(User $user, InfusionDrug $infusion): bool|Response
    {
        return ! $user->isSuperuser()
            && $user->organization_id === $infusion->organization_id
            && $infusion->status === 'published'
            && $infusion->superseded_at === null
            ? true
            : Response::denyAsNotFound();
    }

    public function publish(User $user, InfusionDrug $infusion): bool|Response
    {
        return ! $user->isSuperuser()
            && $user->organization_id === $infusion->organization_id
            && $infusion->status === 'draft'
            ? true
            : Response::denyAsNotFound();
    }

    public function copy(User $user, InfusionDrug $infusion): bool|Response
    {
        return $user->organization_id !== null
            && $user->organization_id !== $infusion->organization_id
            && $infusion->status === 'published'
            && $infusion->superseded_at === null
            && ! $infusion->trashed()
            ? true
            : Response::denyAsNotFound();
    }
}
