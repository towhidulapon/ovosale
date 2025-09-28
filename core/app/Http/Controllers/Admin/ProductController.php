<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ProductOperation;
use App\Traits\RecycleBinManager;


class ProductController extends Controller
{
    use ProductOperation, RecycleBinManager;

    public function printLabel()
    {
        $pageTitle = "Print Label";
        return view('admin.product.label', compact('pageTitle'));
    }
}
