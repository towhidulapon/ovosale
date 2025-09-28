<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Traits\SaleOperation;

class SaleController extends Controller
{
    use SaleOperation;

    public function print($id)
    {
        $sale = Sale::withSum('payments', 'amount')->where("id", $id)->with("warehouse", "customer")->first();
        if (!$sale) {
            $message[] = "The sale is not found";
            return jsonResponse('not_found', 'error', $message);
        }
        $view      = "admin.sale.invoice";
        $message[] = "Print Invoice";

        if (request()->invoice_type == 'pos') {
            $view = "admin.sale.pos_invoice";
        }

        return jsonResponse('print', 'success', $message, [
            'html' => view($view, compact('sale'))->render()
        ]);
    }
}
