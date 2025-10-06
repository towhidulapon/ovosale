<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanOrder extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
