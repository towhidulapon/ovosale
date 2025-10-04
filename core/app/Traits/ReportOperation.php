<?php

namespace App\Traits;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Expense;
use App\Models\ProductDetail;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Transaction;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ReportOperation {

    public function invoiceWiseReport() {
        $pageTitle = 'Invoice Wise Report';
        $view      = "Template::user.reports.invoice_wise_profit";

        $invoicesWise = Sale::query()
            ->with(['customer'])
            ->where('user_id', auth()->id())
            ->withSum('saleDetails as total_sales_price', DB::raw('sale_price * quantity'))
            ->withSum('saleDetails as total_purchase_price', DB::raw('purchase_price * quantity'))
            ->withSum('saleDetails as gross_profit', DB::raw('(sale_price - purchase_price) * quantity'))
            ->searchable(['invoice_number', 'customer:name'])
            ->dateFilter('sale_date')
            ->paginate(getPaginate());

        $widget['total_invoices'] = $invoicesWise->count();
        $widget['total_sale']     = $invoicesWise->sum('total_sales_price');
        $widget['total_purchase'] = $invoicesWise->sum('total_purchase_price');
        $widget['gross_profit']   = $invoicesWise->sum('gross_profit');

        return responseManager("invoice_wise_report", $pageTitle, 'success', compact('pageTitle', 'view', 'invoicesWise', 'widget'));
    }

    public function productWiseReport() {
        $pageTitle = 'Product Wise Report';
        $view      = "Template::user.reports.product_wise_profit";

        $productsWise = ProductDetail::query()
            ->with(['product'])
            ->withSum('salesDetails as total_sales_quantity', 'quantity')
            ->withSum('salesDetails as total_sales_price', DB::raw('sale_price * quantity'))
            ->withSum('salesDetails as total_purchase_price', DB::raw('purchase_price * quantity'))
            ->withSum('salesDetails as gross_profit', DB::raw('(sale_price - purchase_price) * quantity'))
            ->searchable(['product:name', 'sku'])
            ->whereHas('salesDetails', function ($query) {
                $query->dateFilter('created_at');
            })
            ->paginate(getPaginate());

        $widget['sales_quantity'] = $productsWise->sum('total_sales_quantity');
        $widget['total_sale']     = $productsWise->sum('total_sales_price');
        $widget['total_purchase'] = $productsWise->sum('total_purchase_price');
        $widget['gross_profit']   = $productsWise->sum('gross_profit');

        return responseManager("product_wise_report", $pageTitle, 'success', compact('pageTitle', 'view', 'productsWise', 'widget'));
    }

    public function saleReport() {
        $pageTitle = 'Sales Report';
        $view      = "Template::user.reports.sale";
        $baseQuery = Sale::with("warehouse", "customer")
            ->where('user_id', auth()->id())
            ->withSum('payments', 'amount')
            ->withSum('saleDetails as total_purchase_value', 'purchase_price')
            ->withCount('saleDetails')
            ->searchable(["invoice_number"])
            ->dateFilter('sale_date')
            ->filter(['customer_id']);

        $sales = $baseQuery->paginate(getPaginate());

        return responseManager("sale_report", $pageTitle, 'success', compact('pageTitle', 'view', 'sales'));
    }

    public function purchaseReport() {
        $pageTitle = 'Purchase Report';
        $view      = "Template::user.reports.purchase";
        $baseQuery = Purchase::with("warehouse", "supplier")
            ->where('user_id', auth()->id())
            ->withSum('supplierPayments', 'amount')
            ->searchable(["invoice_number"])
            ->dateFilter('purchase_date')
            ->filter(['supplier_id']);

        $purchases = $baseQuery->paginate(getPaginate());

        return responseManager("purchase_report", $pageTitle, 'success', compact('pageTitle', 'view', 'purchases'));
    }

    public function stockReport() {
        $pageTitle  = 'Stock Report';
        $view       = "Template::user.reports.stock";
        $user       = auth()->user();
        $warehouses = Warehouse::where('user_id', $user->id)->orderBy('name')->get();
        $brands     = Brand::where('user_id', $user->id)->orderBy('name')->get();
        $categories = Category::where('user_id', $user->id)->orderBy('name')->get();

        $selectWarehouse = request()->warehouse_id ? Warehouse::where('id', request()->warehouse_id)->firstOrFailWithApi('Warehouse') : $warehouses->first();

        $baseQuery = ProductDetail::with('product', "product.unit", 'productStock.warehouse')->whereHas('product', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->withSum([
                'productStock' => function ($q) use ($selectWarehouse) {
                    $q->where('warehouse_id', $selectWarehouse->id ?? 0);
                },
            ], 'stock')
            ->orderBy('product_stock_sum_stock', 'desc');

        if (!$selectWarehouse) {
            $baseQuery->whereRaw("1=0"); // for empty result
        }

        $products = $baseQuery->searchable(['product:name', 'sku'])->filter(['product:brand_id', 'product:category_id'])->paginate(getPaginate());

        return responseManager("stock_report", $pageTitle, 'success', compact('pageTitle', 'view', 'products', 'warehouses', 'brands', 'categories', 'selectWarehouse'));
    }

    public function expenseReport() {
        $pageTitle = 'Expense Report';
        $view      = "Template::user.reports.expense";
        $baseQuery = Expense::with("category")
            ->where('user_id', auth()->id())
            ->searchable(["category:name"])
            ->dateFilter('expense_date')
            ->filter(['category_id']);

        $expenses = $baseQuery->paginate(getPaginate());

        return responseManager("expense_report", $pageTitle, 'success', compact('pageTitle', 'view', 'expenses'));
    }

    public function transaction(Request $request) {
        $pageTitle = 'Transaction History';
        $baseQuery = Transaction::searchable(['trx'])->filter(['trx_type', 'remark', 'payment_account_id'])->dateFilter()->orderBy('id', getOrderBy());

        if (request()->payment_type) {
            $baseQuery->whereHas('paymentAccount', function ($q) {
                $q->where('payment_type_id', request()->payment_type);
            });
        }
        $transactions = $baseQuery->with('paymentAccount.paymentType')->paginate(getPaginate());
        return responseManager("transaction_report", $pageTitle, 'success', compact('pageTitle', 'transactions'));
    }
}
