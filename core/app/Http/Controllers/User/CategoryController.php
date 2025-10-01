<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\CategoryOperation;
use App\Traits\RecycleBinManager;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use CategoryOperation, RecycleBinManager;
}
