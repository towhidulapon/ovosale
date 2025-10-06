<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\GatewayCurrency;
use App\Models\PlanPurchase;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PlanSubscribeController extends Controller
{
    public function list()
    {
        $pageTitle = "Subscription Plans";
        $subscriptionPlans = SubscriptionPlan::orderBy('id', 'desc')->active()->paginate(getPaginate());
        return view('Template::user.subscription.list', compact('pageTitle', 'subscriptionPlans'));
    }

    public function purchasedList()
    {
        $pageTitle = "Purchased Plans";
        $purchasedPlans = PlanPurchase::where('user_id', auth()->id())->where('status', Status::PLAN_PURCHASE_SUCCESS)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.subscription.subscribed', compact('pageTitle', 'purchasedPlans'));
    }

    public function planPurchase($id){
        $pageTitle = "Plan Purchase";
        $plan = SubscriptionPlan::findOrFail($id);
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();

        return view('Template::user.subscription.purchase', compact('pageTitle', 'plan', 'gatewayCurrency'));
    }

    public function planPurchaseInsert(Request $request){
        $request->validate([
            'amount' => 'required|numeric',
            'gateway' => 'required',
            'currency' => 'required',
        ]);

        $user = auth()->user();

        $planPurchase = new PlanPurchase();
        $planPurchase->user_id = $user->id;
        $planPurchase->subscription_plan_id = $request->plan_id;
        $planPurchase->status = Status::PLAN_PURCHASE_INITIATE;
        $planPurchase->save();

        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code',$request->gateway)->where('currency', $request->currency)->first();

        if(!$gate){
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        (new PaymentController())->insertDepositData($gate, $request->amount, $planPurchase->id);

        return to_route('user.deposit.confirm');
    }

    public function confirmPurchase($user, $planPurchaseId, $amount){

        $planPurchase = PlanPurchase::findOrFail($planPurchaseId);
        $planPurchase->status= Status::PLAN_PURCHASE_SUCCESS;
        $planPurchase->save();

        $user->balance -= $amount;
        $user->save();

        $userTransaction               = new Transaction();
        $userTransaction->user_id      = $user->id;
        $userTransaction->amount       = $amount;
        $userTransaction->post_balance = $user->balance;
        $userTransaction->trx_type     = '-';
        $userTransaction->details      = 'Plan Purchased';
        $userTransaction->trx          = getTrx();
        $userTransaction->remark       = 'plan_purchase';
        $userTransaction->save();

        notify($user, 'PLAN_PURCHASE', [
            'amount'         => showAmount($amount),
            'plan'           => $planPurchase->subscriptionPlan->name,
            'transaction_id' => $userTransaction->trx,
            'date'           => showDateTime($userTransaction->created_at),
            'post_balance'   => showAmount($user->balance),
        ]);
    }
}
