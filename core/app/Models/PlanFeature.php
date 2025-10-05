<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    use GlobalStatus;

    public function plans() {
        return $this->belongsToMany(SubscriptionPlan::class, 'features', 'feature_id', 'plan_id');
    }
}
