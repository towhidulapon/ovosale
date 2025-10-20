<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\PlanPurchase;

class ManageSubscriptionController extends Controller
{
    public function purchase()
    {
        $pageTitle      = 'Purchased Subscriptions';
        $purchasedPlans = PlanPurchase::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.purchase', compact('pageTitle', 'purchasedPlans'));
    }

    public function active()
    {
        $pageTitle      = 'Active Subscriptions';
        $purchasedPlans = PlanPurchase::where('status', Status::PLAN_ACTIVATE)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.purchase', compact('pageTitle', 'purchasedPlans'));
    }

    public function expired()
    {
        $pageTitle      = 'Expired Subscriptions';
        $purchasedPlans = PlanPurchase::where('status', Status::PLAN_EXPIRED)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.purchase', compact('pageTitle', 'purchasedPlans'));
    }

    public function pending()
    {
        $pageTitle      = 'Pending Subscriptions';
        $purchasedPlans = PlanPurchase::where('status', Status::PLAN_PENDING)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.purchase', compact('pageTitle', 'purchasedPlans'));
    }
    public function trial()
    {
        $pageTitle      = 'Pending Subscriptions';
        $purchasedPlans = PlanPurchase::where('status', Status::PLAN_ON_TRIAL)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscription.purchase', compact('pageTitle', 'purchasedPlans'));
    }

    public function details($id)
    {
        $pageTitle = 'Plan Details';
        $plan      = PlanPurchase::findOrFail($id);
        return view('admin.subscription.details', compact('pageTitle', 'plan'));
    }

}
