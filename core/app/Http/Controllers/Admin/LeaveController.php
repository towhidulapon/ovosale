<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LeaveRequestOperation;
use App\Traits\LeaveTypeOperation;
use App\Traits\RecycleBinManager;

class LeaveController extends Controller
{
    use LeaveRequestOperation,LeaveTypeOperation,RecycleBinManager;
}
