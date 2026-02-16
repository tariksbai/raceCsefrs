<?php

namespace App\Policies;

use App\Models\ExternalForm;
use App\Models\User;

class ExternalFormPolicy
{
    /**
     * Any authenticated user can view the list of external forms.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin, or public form, or user belongs to the form's group.
     */
    public function view(User $user, ExternalForm $externalForm): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($externalForm->is_public) {
            return true;
        }

        if ($externalForm->group_id && $user->groups()->where('groups.id', $externalForm->group_id)->exists()) {
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
    public function update(User $user, ExternalForm $externalForm): bool
    {
        return $user->isAdmin() || $user->hasPermission('edit_form');
    }

    /**
     * User has 'delete_form' permission or is admin.
     */
    public function delete(User $user, ExternalForm $externalForm): bool
    {
        return $user->isAdmin() || $user->hasPermission('delete_form');
    }
}
