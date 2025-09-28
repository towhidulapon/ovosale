<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\CouponOperation;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use CouponOperation;
}
