<?php

namespace App\Policies;

use App\Models\InternalForm;
use App\Models\User;

class InternalFormPolicy
{
    /**
     * Any authenticated user can view the list of internal forms.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin, or public form, or user belongs to the form's group.
     */
    public function view(User $user, InternalForm $internalForm): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($internalForm->is_public) {
            return true;
        }

        if ($internalForm->group_id && $user->groups()->where('groups.id', $internalForm->group_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * User has 'create_form' permission or is admin.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('create_form');
    }

    /**
     * User has 'edit_form' permission or is admin.
     */
    public function update(User $user, InternalForm $internalForm): bool
    {
        return $user->isAdmin() || $user->hasPermission('edit_form');
    }

    /**
     * User has 'delete_form' permission or is admin.
     */
    public function delete(User $user, InternalForm $internalForm): bool
    {
        return $user->isAdmin() || $user->hasPermission('delete_form');
    }

    /**
     * Admin, or public form, or user belongs to the form's group, or form allows anonymous.
     */
    public function respond(User $user, InternalForm $internalForm): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($internalForm->is_public) {
            return true;
        }

        if ($internalForm->group_id && $user->groups()->where('groups.id', $internalForm->group_id)->exists()) {
            return true;
        }

        if ($internalForm->allow_anonymous) {
            return true;
        }

        return false;
    }
}
