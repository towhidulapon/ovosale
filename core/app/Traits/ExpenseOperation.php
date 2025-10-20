<?php

namespace App\Traits;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentAccount;
use App\Models\PaymentType;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

trait ExpenseOperation
{
    public function list()
    {
        $user = getParentUser();

        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        $baseQuery = Expense::orderBy('id', getOrderBy())->whereIn('user_id', $userIds)->with('paymentType', 'paymentAccount');
        $pageTitle = 'Manage Expense';
        $view      = "Template::user.expense.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Expense");
        }

        $expenses          = (clone $baseQuery)->dateFilter('expense_date')->searchable(['reference_no', 'category:name'])->trashFilter()->paginate(getPaginate());
        $expenseCategories = ExpenseCategory::whereIn('user_id', $userIds)->active()->get();
        $widget            = [];

        $widget['today_expense']             = (clone $baseQuery)->where('expense_date', now()->format("Y-m-d"))->sum('amount');
        $widget['yesterday_expense']         = (clone $baseQuery)->where('expense_date', now()->subDay()->format("Y-m-d"))->sum('amount');
        $widget['this_week_expense']         = (clone $baseQuery)->where('expense_date', ">=", now()->startOfWeek()->format("Y-m-d"))->sum('amount');
        $widget['last_7days_week_expense']   = (clone $baseQuery)->where('expense_date', ">=", now()->subDays(7)->format("Y-m-d"))->sum('amount');
        $widget['this_month_expense']        = (clone $baseQuery)->where('expense_date', ">=", now()->startOfMonth()->format("Y-m-d"))->sum('amount');
        $widget['last_30days_month_expense'] = (clone $baseQuery)->where('expense_date', ">=", now()->subDays(30)->format("Y-m-d"))->sum('amount');
        $widget['all_expense']               = (clone $baseQuery)->sum('amount');
        $widget['last_expense_amount']       = (clone $baseQuery)->orderby('id', 'desc')->first()?->amount;

        $paymentMethods = PaymentType::active()->with('paymentAccounts', function ($q) {
            $q->active();
        })->get();

        return responseManager("expenses", $pageTitle, 'success', compact('expenses', 'view', 'pageTitle', 'expenseCategories', 'widget', 'paymentMethods'), ['expenseCategories', 'paymentMethods']);
    }

    public function save(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';

        $request->validate([
            'expense_date'    => 'required|date',
            'reference_no'    => 'nullable|string',
            'comment'         => 'nullable|string',
            'expense_purpose' => 'required|integer',
            'amount'          => 'required|numeric|gt:0',
            'payment_type'    => "$isRequired|integer|exists:payment_types,id",
            'payment_account' => "$isRequired|integer|exists:payment_accounts,id",
            'attachment'      => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf', 'docx'])],
        ]);

        if ($id) {
            $expense          = Expense::where('id', $id)->firstOrFailWithApi('expense');
            $message          = "Expense updated successfully";
            $remark           = "expense-updated";
            $oldExpenseAmount = $expense->amount;
        } else {
            $expense = new Expense();
            // $expense->added_by           = getAdmin('id');
            $message                     = "Expense added successfully";
            $remark                      = "expense-add";
            $expense->user_id            = auth()->id();
            $expense->payment_type_id    = $request->payment_type;
            $expense->payment_account_id = $request->payment_account;
            $oldExpenseAmount            = 0;
        }

        $expense->expense_date = $request->expense_date;
        $expense->category_id  = $request->expense_purpose;
        $expense->reference_no = $request->reference_no ?? null;
        $expense->comment      = $request->comment ?? null;
        $expense->amount       = $request->amount;

        if ($request->hasFile('attachment')) {
            $expense->attachment = fileUploader($request->attachment, getFilePath("expense_attachment"));
        }

        $expense->save();

        if (!$id) {
            $paymentAccount = PaymentAccount::where('id', $request->payment_account)->first();
            $details        = "The expense amount subtract from the payment account";
            createTransaction($paymentAccount, '-', $request->amount, 'balance_subtract', $details);
        } else {
            if ($oldExpenseAmount != $expense->amount) {

                $paymentAccount = PaymentAccount::where('id', $expense->payment_account_id)->first();
                $details        = "Balance adjustment for the update expense";

                if ($expense->amount > $oldExpenseAmount) {
                    $amount = $expense->amount - $oldExpenseAmount;
                    createTransaction($paymentAccount, '-', $amount, 'balance_subtract', $details);
                } else {
                    $amount = $oldExpenseAmount - $expense->amount;
                    createTransaction($paymentAccount, '+', $amount, 'balance_added', $details);
                }
            }
        }

        // adminActivity($remark, get_class($expense), $expense->id);
        return responseManager("expense", $message, 'success', compact('expense'));
    }

    public function status($id)
    {
        return Expense::changeStatus($id);
    }
}
