<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;
use App\Traits\RecycleBinManager;
use App\Traits\SupplierOperation;

class SupplierController extends Controller
{
    use SupplierOperation, RecycleBinManager;

    public function lazyLoadingData()
    {

        $user     = getParentUser();
        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        if (!in_array($user->id, $userIds)) {
            $userIds[] = $user->id;
        }

        $data = Supplier::whereIn('user_id', $userIds)->searchable(['email', 'name'])->orderBy('id', 'desc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'data'    => $data,
            'more'    => $data->hasMorePages(),
        ]);
    }
}
