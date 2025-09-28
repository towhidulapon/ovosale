<?php

namespace App\Traits;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

trait ExpenseCategoryOperation
{
    public function list()
    {
        $baseQuery = ExpenseCategory::searchable(['name'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Expense Category';
        $view      = "admin.expense.category.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "ExpenseCategory");
        }

        $expenseCategories = $baseQuery->paginate(getPaginate());
        return responseManager("expenseCategories", $pageTitle, 'success', compact('expenseCategories', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $id,
        ]);

        if ($id) {
            $expenseCategory = ExpenseCategory::where('id', $id)->firstOrFailWithApi('expense category');
            $message         = "Expense category updated successfully";
            $remark          = "expense-category-updated";
        } else {
            $expenseCategory = new ExpenseCategory();
            $message         = "Expense category saved successfully";
            $remark          = "expense-category-added";
        }

        $expenseCategory->name = $request->name;
        $expenseCategory->save();

        adminActivity($remark, get_class($expenseCategory), $expenseCategory->id);

        return responseManager("expenseCategory", $message, 'success', compact('expenseCategory'));
    }

    public function status($id)
    {
        return ExpenseCategory::changeStatus($id);
    }
}
