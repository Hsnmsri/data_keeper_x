<?php

namespace App\Classes\Service;

use App\Models\Role;
use App\Models\User;

class RolePermissionService
{
    public static function hasPermission($user_id, $permission_id): bool
    {
        // get user
        $user = User::find($user_id);
        $user_permissions = Role::find($user->role_id)->permissions();
        dd($user_permissions);
        return true;
    }
}
