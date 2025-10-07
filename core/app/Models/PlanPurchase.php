<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class PlanPurchase extends Model
{
    use GlobalStatus;
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan() {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
