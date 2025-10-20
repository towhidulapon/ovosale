<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Traits\CustomerOperation;
use App\Traits\RecycleBinManager;

class CustomerController extends Controller
{
    use CustomerOperation, RecycleBinManager;

    public function lazyLoadingData()
    {
        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        if (!in_array($user->id, $userIds)) {
            $userIds[] = $user->id;
        }

        $data = Customer::whereIn('user_id', $userIds)->searchable(['email', 'name', 'mobile'])->orderBy('id', 'asc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'data'    => $data,
            'more'    => $data->hasMorePages(),
        ]);
    }
}
