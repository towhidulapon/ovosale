<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;

trait CouponOperation
{
    public function list()
    {
        $user     = getParentUser();
        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        $coupons   = Coupon::whereIn('user_id', $userIds)->searchable(['name'])->orderBy('id', getOrderBy())->trashFilter()->paginate(getPaginate());
        $pageTitle = 'Manage Coupon';
        $view      = "Template::user.coupon.list";

        return responseManager("coupons", $pageTitle, 'success', compact('coupons', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'coupon_name'        => 'required|string|max:40',
            'coupon_code'        => 'required|string|max:40|unique:coupons,code,' . $id,
            'minimum_amount'     => 'required|numeric|gt:0',
            'discount_type'      => 'required|in:' . Status::DISCOUNT_PERCENT . "," . Status::DISCOUNT_FIXED,
            'amount'             => 'required|numeric|gt:0',
            'start_from'         => 'required|date',
            'maximum_using_time' => 'required|integer|gte:1',
            'end_at'             => 'required|date|after_or_equal:start_from',
        ]);

        if ($id) {
            $coupon  = Coupon::where('id', $id)->firstOrFailWithApi('Coupon');
            $message = "Coupon updated successfully";
            $remark  = "coupon-updated";
        } else {
            $coupon  = new Coupon();
            $message = "Coupon saved successfully";
            $remark  = "coupon-added";
            $coupon->user_id            = auth()->user()->id;
        }

        $coupon->name               = $request->coupon_name;
        $coupon->code               = $request->coupon_code;
        $coupon->start_from         = $request->start_from;
        $coupon->end_at             = $request->end_at;
        $coupon->minimum_amount     = $request->minimum_amount;
        $coupon->discount_type      = $request->discount_type;
        $coupon->amount             = $request->amount;
        $coupon->maximum_using_time = $request->maximum_using_time;
        $coupon->save();

        // adminActivity($remark, get_class($coupon), $coupon->id);
        return responseManager("coupon", $message, 'success', compact('coupon'));
    }

    public function status($id)
    {
        return Coupon::changeStatus($id);
    }
}
