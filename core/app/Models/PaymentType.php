<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentType extends Model
{
    use GlobalStatus, SoftDeletes;

    protected $guarded  = ['id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function paymentAccounts()
    {
        return $this->hasMany(PaymentAccount::class, 'payment_type_id');
    }
}
