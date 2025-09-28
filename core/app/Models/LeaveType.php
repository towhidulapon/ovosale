<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\RecycleBinManager;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use GlobalStatus, RecycleBinManager;

    protected $guarded  = ['id'];
}
