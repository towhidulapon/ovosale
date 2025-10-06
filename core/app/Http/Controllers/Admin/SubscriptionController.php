<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\PlanFeature;
use App\Models\PlanOrder;
use App\Models\PlanPurchase;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller {
    public function plans() {
        $pageTitle = 'Subscription Plans';
        $subscriptionPlans     = SubscriptionPlan::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.index', compact('pageTitle', 'subscriptionPlans'));
    }

    public function create() {
        $pageTitle = 'Add Plan';
        $planFeatures = PlanFeature::orderBy('id', 'desc')->get();
        return view('admin.subscription.add', compact('pageTitle', 'planFeatures'));
    }

    public function save(Request $request, $id = 0) {
        $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|in:1,2,3,4,5',
            'price' => 'required|numeric|gt:0',
            'warehouse_number' => 'required|integer|gt:0',
            'trial_days' => 'required|integer|gt:0',
            'features' => 'required|array|min:1',
            'features.*' => 'exists:plan_features,id',
        ]);

        if($id) {
            $plan = SubscriptionPlan::findOrFail($id);
            $plan->name = $request->name;
            $plan->frequency = $request->frequency;
            $plan->price = $request->price;
            $plan->warehouse_number = $request->warehouse_number;
            $plan->trial_days = $request->trial_days;
            $message = "Plan updated successfully";
        } else {
            $plan = new SubscriptionPlan();
            $plan->name = $request->name;
            $plan->frequency = $request->frequency;
            $plan->price = $request->price;
            $plan->warehouse_number = $request->warehouse_number;
            $plan->trial_days = $request->trial_days;
            $plan->save();
            $message = "Plan created successfully";
        }

        $plan->save();
        $plan->features()->sync($request->features);

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function edit($id) {
        $pageTitle = 'Edit Plan';
        $plan = SubscriptionPlan::with('features')->findOrFail($id);
        $planFeatures = PlanFeature::all();
        return view('admin.subscription.edit', compact('pageTitle', 'plan', 'planFeatures'));
    }

    public function purchase(){
        $pageTitle = 'Purchased Subscriptions';
        $purchasedPlans = PlanPurchase::where('status', Status::PLAN_PURCHASE_SUCCESS)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.purchase', compact('pageTitle', 'purchasedPlans'));
    }

    public function status($id) {
        return SubscriptionPlan::changeStatus($id);
    }
}
