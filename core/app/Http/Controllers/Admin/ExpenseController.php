<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ExpenseOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use ExpenseOperation, RecycleBinManager;
}
