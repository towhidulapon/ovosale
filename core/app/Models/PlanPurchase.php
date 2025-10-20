<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PlanPurchase extends Model
{
    use GlobalStatus;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::PLAN_ACTIVATE) {
                $html = '<span class="badge badge--success">' . trans('Activate') . '</span>';
            } elseif ($this->status == Status::PLAN_EXPIRED) {
                $html = '<span class="badge badge--danger">' . trans('Expired') . '</span>';
            } elseif($this->status == Status::PLAN_PENDING){
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            }
            else {
                $html = '<span class="badge badge--info">' . trans('Trial') . '</span>';
            }
            return $html;
        });
    }
}
