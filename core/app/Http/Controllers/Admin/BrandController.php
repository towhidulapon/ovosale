<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\BrandOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use BrandOperation, RecycleBinManager;
}
