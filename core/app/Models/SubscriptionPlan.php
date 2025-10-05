<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use GlobalStatus;

    public function features() {
        return $this->belongsToMany(PlanFeature::class, 'features', 'plan_id', 'feature_id');
    }
}
