<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\RecycleBinManager;
use App\Traits\TaxOperation;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    use TaxOperation, RecycleBinManager;
}
