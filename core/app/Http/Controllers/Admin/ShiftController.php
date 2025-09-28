<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\RecycleBinManager;
use App\Traits\ShiftOperation;

class ShiftController extends Controller
{
    use ShiftOperation, RecycleBinManager;
}
