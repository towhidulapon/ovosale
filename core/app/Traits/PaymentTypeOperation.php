<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\PaymentType;
use Illuminate\Http\Request;

trait PaymentTypeOperation
{
    public function list()
    {
        $baseQuery = PaymentType::searchable(['name'])->orderBy('id', getOrderBy())->trashFilter();
        $pageTitle = 'Manage Payment Type';
        $view      = "admin.payment_type.list";

        if (request()->export) {
            return exportData($baseQuery, request()->export, "PaymentType");
        }

        $paymentTypes = $baseQuery->paginate(getPaginate());
        return responseManager("payment_type", $pageTitle, 'success', compact('paymentTypes', 'view', 'pageTitle'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_types,name,' . $id,
        ]);

        if ($id) {
            $paymentType = PaymentType::where('id', $id)->where('is_default', Status::NO)->firstOrFailWithApi('Payment type');
            $message     = "Payment type updated successfully";
            $remark      = "payment-type-updated";

            if ($paymentType->is_default != Status::YES) {
                $paymentType->name = $request->name;
                $paymentType->slug = slug($request->name);
            }
        } else {
            $paymentType       = new PaymentType();
            $message           = "Payment type saved successfully";
            $remark            = "payment-type-added";
            $paymentType->name = $request->name;
            $paymentType->slug = slug($request->name);
        }

        $paymentType->save();

        adminActivity($remark, get_class($paymentType), $paymentType->id);
        return responseManager("payment_type", $message, 'success', compact('paymentType'));
    }

    public function status($id)
    {
        $paymentType = PaymentType::where('id', $id)->where('is_default', Status::NO)->firstOrFailWithApi('Payment type');
        if ($paymentType->is_default != Status::YES) {
            return PaymentType::changeStatus($id);
        }
    }
}
