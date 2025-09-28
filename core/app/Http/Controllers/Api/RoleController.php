<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\RoleOperation;

class RoleController extends Controller
{
    use RoleOperation;

    public function assignPermission()
    {
        $user        = auth()->user();
        $permissions = collect($user->getAllPermissions())->pluck('name')->toArray();

        $message[] = "Assign Permission List";

        return jsonResponse('success', 'success', $message, [
            'assign_permissions' => $permissions
        ]);
    }
}
