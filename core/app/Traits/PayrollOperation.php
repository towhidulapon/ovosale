<?php

namespace App\Traits;

use App\Models\Employee;
use App\Models\PaymentAccount;
use App\Models\PaymentType;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;

trait PayrollOperation
{
    public function list()
    {
        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        $baseQuery = Payroll::whereIn('user_id', $userIds)->searchable(['employee:name', 'employee:phone'])->with('employee')->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Payroll';
        $view      = "Template::user.hrm.payroll.list";
        if (request()->export) {
            return exportData($baseQuery, request()->export, "payroll", "A4 landscape");
        }
        $payrolls  = $baseQuery->paginate(getPaginate());
        $employees = Employee::whereHas('company', function ($q) use ($userIds) {
            $q->whereIn('user_id', $userIds);
        })
            ->active()
            ->orderBy('name')
            ->get();
        $paymentMethods = PaymentType::whereIn('user_id', $userIds)->with('paymentAccounts')->active()->orderBy('name')->get();

        return responseManager("payroll", $pageTitle, 'success', compact('payrolls', 'view', 'pageTitle', 'employees', 'paymentMethods'));
    }

    public function save(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';
        $request->validate(
            [
                'employee_id'        => 'required|exists:employees,id',
                'date'               => 'required|date',
                'amount'             => 'required|numeric|gt:0',
                'payment_method_id'  => "$isRequired|exists:payment_types,id",
                'payment_account_id' => "$isRequired|exists:payment_accounts,id",

            ],
            [
                'employee_id.required'        => 'Please select a employee',
                'payment_method_id.required'  => 'Please select a payment method',
                'payment_account_id.required' => 'Please select a payment account',
            ]
        );

        if ($id) {
            $payroll   = Payroll::where('id', $id)->firstOrFailWithApi('payroll');
            $message   = "Payroll updated successfully";
            $remark    = "payroll-updated";
            $oldAmount = $payroll->amount;
        } else {
            $payroll                     = new Payroll();
            $message                     = "Payroll saved successfully";
            $remark                      = "payroll-added";
            $payroll->payment_method_id  = $request->payment_method_id;
            $payroll->payment_account_id = $request->payment_account_id;
            $oldAmount                   = 0;
            $payroll->user_id            = auth()->id();
        }

        $payroll->employee_id = $request->employee_id;
        $payroll->date        = $request->date;
        $payroll->amount      = $request->amount;

        if (!$id) {
            $paymentAccount = PaymentAccount::where('id', $request->payment_account_id)->first();
            $details        = "The payroll amount subtract from the payment account";
            if ($paymentAccount->balance < $request->amount) {
                return responseManager("payment_account", "Not the enough balance to the payment account.", 'error');
            }
            createTransaction($paymentAccount, '-', $request->amount, 'balance_subtract', $details);
        } else {
            if ($oldAmount != $payroll->amount) {

                $paymentAccount = PaymentAccount::where('id', $payroll->payment_account_id)->first();
                $details        = "Balance adjustment for the update of the payroll amount";

                if ($payroll->amount > $oldAmount) {
                    $amount = $payroll->amount - $oldAmount;
                    if ($paymentAccount->balance < $amount) {
                        return responseManager("payment_account", "Not the enough balance to the payment account.", 'error');
                    }
                    createTransaction($paymentAccount, '-', $amount, 'balance_subtract', $details);
                } else {
                    $amount = $oldAmount - $payroll->amount;
                    createTransaction($paymentAccount, '+', $amount, 'balance_added', $details);
                }
            }
        }

        $payroll->save();

        // adminActivity($remark, get_class($payroll), $payroll->id);
        return responseManager("payroll", $message, 'success', compact('payroll'));
    }
}
