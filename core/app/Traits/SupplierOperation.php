<?php

namespace App\Traits;

use App\Models\PaymentAccount;
use App\Models\PaymentType;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Http\Request;

trait SupplierOperation
{
    public function list()
    {
        $user     = getParentUser();
        $staffIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
        $userIds  = array_merge([$user->id], $staffIds);

        $baseQuery = Supplier::whereIn('user_id', $userIds)->searchable(['name', 'email'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Supplier';
        $view      = "Template::user.supplier.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Supplier", "A4 landscape");
        }

        $suppliers = $baseQuery->paginate(getPaginate());
        return responseManager("supplier", $pageTitle, 'success', compact('suppliers', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|string|email|max:255|unique:suppliers,email,' . $id,
            'mobile'       => 'nullable|string|max:255|unique:suppliers,mobile,' . $id,
            'company_name' => 'required|string|max:255|unique:suppliers,company_name,' . $id,
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:255',
            'state'        => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:255',
            'zip'          => 'nullable|string|max:40',
            'postcode'     => 'nullable|string|max:40',
        ]);

        if ($id) {
            $supplier = Supplier::where('id', $id)->firstOrFailWithApi('supplier');
            $message  = "Supplier updated successfully";
            $remark   = "supplier-updated";
        } else {
            $supplier = new Supplier();
            $message  = "Supplier saved successfully";
            $remark   = "supplier-added";
            $supplier->user_id      = auth()->id();
        }

        $supplier->company_name = $request->company_name;
        $supplier->name         = $request->name;
        $supplier->email        = $request->email;
        $supplier->mobile       = $request->mobile;
        $supplier->address      = $request->address;
        $supplier->city         = $request->city;
        $supplier->state        = $request->state;
        $supplier->country      = $request->country;
        $supplier->zip          = $request->zip;
        $supplier->postcode     = $request->postcode;
        $supplier->save();

        // adminActivity($remark, get_class($supplier), $supplier->id);

        return responseManager("supplier", $message, 'success', compact('supplier'));
    }

    public function status($id)
    {
        return Supplier::changeStatus($id);
    }

    public function view(Request $request, $id)
    {

        $pageTitle = "Supplier Information";
        $view      = "Template::user.supplier.view";

        $supplier  = Supplier::where('user_id', auth()->id())->where('id', $id)->firstOrFailWithApi('supplier');
        $baseQuery = SupplierPayment::where('supplier_id', $supplier->id);

        $widget                   = [];
        $widget['total_purchase'] = Purchase::where('user_id', auth()->id())->where('supplier_id', $supplier->id)->sum('total');
        $widget['total_payment']  = (clone $baseQuery)->sum('amount');
        $widget['total_due']      = $widget['total_purchase'] - $widget['total_payment'];
        $widget['today_payment']  = (clone $baseQuery)->where('payment_date', now()->format('Y-m-d'))->sum('amount');

        $purchases = Purchase::where('supplier_id', $supplier->id)
            ->when($request->purchase_search, function ($query, $purchaseSearch) {
                $query->where('invoice_number', 'like', "%{$purchaseSearch}%")
                    ->orWhere('total', 'like', "%{$purchaseSearch}%");
            })
            ->with('warehouse', 'supplier')
            ->latest('id')
            ->paginate(getPaginate(), ['*'], 'purchases_page');

        $payments = (clone $baseQuery)
            ->when($request->payment_search, function ($query, $paymentSearch) {
                $query->where('payment_date', 'like', "%{$paymentSearch}%")
                    ->orWhere('amount', 'like', "%{$paymentSearch}%");
            })
            ->latest('id')
            ->with('paymentType')
            ->paginate(getPaginate(), ['*'], 'payments_page');

        $paymentMethods = PaymentType::active()->with('paymentAccounts', function ($q) {
            $q->active();
        })->get();

        return responseManager("view_supplier", $pageTitle, 'success', compact('supplier', 'view', 'pageTitle', 'widget', 'purchases', 'payments', 'paymentMethods'));
    }

    public function addPayment(Request $request, $id)
    {

        $request->validate([
            'paid_amount'     => 'required|numeric|gt:0',
            'paid_date'       => 'required',
            'payment_type'    => "required|integer|exists:payment_types,id",
            'payment_account' => "required|integer|exists:payment_accounts,id",
        ]);

        $supplier = Supplier::where('id', $id)->firstOrFailWithApi('supplier');

        $supplierPayment                  = new SupplierPayment();
        $supplierPayment->supplier_id     = $supplier->id;
        $supplierPayment->amount          = $request->paid_amount;
        $supplierPayment->payment_date    = now()->parse($request->paid_date)->format('Y-m-d');
        $supplierPayment->payment_type_id = $request->payment_type;
        $supplierPayment->payment_type_id = $request->payment_account;
        $supplierPayment->payment_note    = $request->payment_note;
        $supplierPayment->save();

        $paymentAccount = PaymentAccount::where('id', $request->payment_account)->first();

        $details = "The supplier paid amount subtract from the payment account.";
        createTransaction($paymentAccount, '-', $request->paid_amount, 'balance_subtract', $details);

        adminActivity("supplier-payment", get_class($supplierPayment), $supplierPayment->id);
        $notify = "Supplier payment added successfully";

        return responseManager("supplier_payment", $notify, 'success', [
            'supplierPayment' => $supplierPayment,
        ]);
    }
}
