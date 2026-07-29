<?php

namespace App\Policies;

use App\Models\InternalFormResponse;
use App\Models\User;

class ResponsePolicy
{
    /**
     * User has 'export_responses' permission or is admin.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('export_responses');
    }

    /**
     * User has 'export_responses' permission or is admin.
     */
    public function view(User $user, InternalFormResponse $response): bool
    {
        return $user->isAdmin() || $user->hasPermission('export_responses');
    }

    /**
     * User has 'export_responses' permission or is admin.
     */
    public function export(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('export_responses');
    }
}
