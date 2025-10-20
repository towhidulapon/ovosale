<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\PlanPurchase;
use Carbon\Carbon;

class CronController extends Controller {
    public function cron() {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function fetchSubscriptions() {

        $now = Carbon::now();
        $purchasePlans = PlanPurchase::where('status', Status::PLAN_ACTIVATE)->get();
        $expiredPlans = [];

        foreach ($purchasePlans as $plan) {
            $endDate = subscriptionEndDate($plan->created_at, $plan->frequency);

            if ($endDate < $now) {
                $expiredPlans[] = $plan;
            }
        }

        foreach ($expiredPlans as $expiredPlan) {
            $expiredPlan->status = Status::PLAN_EXPIRED;
            $expiredPlan->save();
        }
    }

    public function fetchTrial() {

        $now = Carbon::now();

        $trialPlans = PlanPurchase::where('status', Status::PLAN_ON_TRIAL)->get();

        $expiredPlans = [];

        foreach ($trialPlans as $plan) {
            $trialEnd = $plan->created_at->addDays($plan->subscriptionPlan->trial_days);

            if ($trialEnd < $now) {
                $expiredPlans[] = $plan;
            }
        }

        foreach ($expiredPlans as $expiredPlan) {
            $expiredPlan->status = Status::PLAN_EXPIRED;
            $expiredPlan->save();
        }
    }
}
