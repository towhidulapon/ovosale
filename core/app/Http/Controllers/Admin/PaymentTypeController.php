<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\PaymentTypeOperation;
use App\Traits\RecycleBinManager;

class PaymentTypeController extends Controller
{
    use PaymentTypeOperation, RecycleBinManager;
}
