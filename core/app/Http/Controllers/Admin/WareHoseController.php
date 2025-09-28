<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\RecycleBinManager;
use App\Traits\WareHouseOperation;
use Illuminate\Http\Request;

class WareHoseController extends Controller
{
    use WareHouseOperation, RecycleBinManager;
}
