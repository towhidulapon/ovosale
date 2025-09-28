<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Traits\RecycleBinManager;
use App\Traits\SupplierOperation;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use SupplierOperation, RecycleBinManager;

    public function  lazyLoadingData()
    {
        $data = Supplier::searchable(['email', 'name'])->orderBy('id', 'desc')->paginate(getPaginate());
        return response()->json([
            'success' => true,
            'data'    => $data,
            'more'    => $data->hasMorePages()
        ]);
    }
}
