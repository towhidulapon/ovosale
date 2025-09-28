<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\RecycleBinManager;
use App\Traits\VariantOperation;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    use VariantOperation, RecycleBinManager;
}
