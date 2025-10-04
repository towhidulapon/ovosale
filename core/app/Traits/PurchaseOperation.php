<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\PaymentAccount;
use App\Models\PaymentType;
use App\Models\ProductDetail;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Tax;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

trait PurchaseOperation {
    public function list() {
        $pageTitle      = "Purchase List";
        $view           = "Template::user.purchase.list";
        $paymentMethods = PaymentType::active()->with("paymentAccounts", function ($q) {
            $q->active();
        })->get();

        $baseQuery      = Purchase::where('user_id', auth()->id())->latest('id');

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Purchase");
        }

        $purchases = (clone $baseQuery)
            ->with("warehouse", "supplier", 'supplierPayments.paymentType')
            ->withSum('supplierPayments', 'amount')
            ->dateFilter('purchase_date')
            ->filter(['supplier_id'])
            ->trashFilter()
            ->searchable(['reference_number', 'invoice_number', 'supplier:name', 'warehouse:name'])
            ->paginate(getPaginate());

        $widget                               = [];
        $widget['today_purchase']             = (clone $baseQuery)->where('purchase_date', now()->format("Y-m-d"))->sum('total');
        $widget['yesterday_purchase']         = (clone $baseQuery)->where('purchase_date', now()->subDay()->format("Y-m-d"))->sum('total');
        $widget['this_week_purchase']         = (clone $baseQuery)->where('purchase_date', ">=", now()->startOfWeek()->format("Y-m-d"))->sum('total');
        $widget['last_7days_week_purchase']   = (clone $baseQuery)->where('purchase_date', ">=", now()->subDays(7)->format("Y-m-d"))->sum('total');
        $widget['this_month_purchase']        = (clone $baseQuery)->where('purchase_date', ">=", now()->startOfMonth()->format("Y-m-d"))->sum('total');
        $widget['last_30days_month_purchase'] = (clone $baseQuery)->where('purchase_date', ">=", now()->subDays(30)->format("Y-m-d"))->sum('total');
        $widget['all_purchase']               = (clone $baseQuery)->sum('total');
        $widget['last_purchase_amount']       = (clone $baseQuery)->orderby('id', 'desc')->first()?->total;


        return responseManager("purchase", $pageTitle, 'success', compact('pageTitle', 'purchases', 'paymentMethods', 'view', 'widget'), ['paymentMethods']);
    }

    public function add() {
        $pageTitle = "Add Purchase";
        $view      = "Template::user.purchase.add";
        extract($this->basicDataForPurchase());
        return responseManager("add_purchase", $pageTitle, 'success', compact('pageTitle', 'warehouses', 'taxes', 'paymentMethods', 'suppliers', 'view'));
    }

    public function edit($id) {
        $pageTitle = "Edit Purchase";
        $purchase  = Purchase::where("user_id", auth()->id())->where("id", $id)->with('purchaseDetails.product', "purchaseDetails.productDetail.attribute", "purchaseDetails.productDetail.variant")->firstOrFailWithApi("purchase");
        $view      = "Template::user.purchase.edit";

        extract($this->basicDataForPurchase());
        return responseManager("edit_purchase", $pageTitle, 'success', compact('pageTitle', 'warehouses', 'taxes', 'paymentMethods', 'suppliers', 'view', 'purchase'));
    }

    public function view($id) {
        $pageTitle = "Purchase Invoice";
        $view      = "Template::user.purchase.view";

        $purchase = Purchase::withSum('supplierPayments', 'amount')
            ->where("user_id", auth()->id())
            ->where("id", $id)
            ->with("warehouse", "supplier", 'supplierPayments.paymentType')
            ->firstOrFailWithApi("purchase");

        return responseManager("view_purchase", $pageTitle, 'success', compact('pageTitle', 'purchase', 'view'));
    }


    public function store(Request $request) {
        $validator = $this->validation($request);
        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $supplier       = Supplier::where('user_id', auth()->id())->where('id', $request->supplier_id)->first();
        $shippingAmount = $request->shipping_amount ?? 0;
        //get the sub total

        $subtotal       = array_reduce($request->purchase_details, function ($carry, $productDetail) {
            return $carry + $productDetail['qty'] * $productDetail['purchase_price'];
        }, 0);

        $discountAmount = 0;
        $discountValue  = $request->discount ?? 0;
        $discountType   = $request->discount_type ?? 0;

        if ($discountValue > 0) {
            if ($discountType == Status::DISCOUNT_PERCENT) {
                $discountAmount = $subtotal * $discountValue / 100;
            } else {
                $discountAmount = $discountValue;
            }
        }

        if ($discountAmount > $subtotal) {
            $notify[] = "The discount amount can not be greater than total";
            return jsonResponse('limit', 'error', $notify);
        }

        $total = $subtotal - $discountAmount + $shippingAmount;

        if ($request->paid_amount && $request->paid_amount > $total) {
            $notify[] = "The paid amount cannot be greater than the total amount";
            return jsonResponse('limit', 'error', $notify);
        }

        try {
            DB::beginTransaction();

            $purchase                   = new Purchase();
            $purchase->user_id          = auth()->id();
            $purchase->invoice_number   = $this->invoiceNumber();
            $purchase->supplier_id      = $supplier->id;
            $purchase->warehouse_id     = $request->warehouse_id;
            $purchase->purchase_date    = now()->parse($request->purchase_date)->format('Y-m-d');
            $purchase->reference_number = $request->reference_no ?? null;

            $purchase->discount_type   = $discountType;
            $purchase->discount_value  = $discountValue;
            $purchase->discount_amount = $discountAmount;

            $purchase->shipping_amount = $shippingAmount;
            $purchase->subtotal        = $subtotal;
            $purchase->total           = $total;
            $purchase->status          = $request->status;
            // $purchase->admin_id        = getAdmin('id');

            if ($request->hasFile('attachment')) {
                try {
                    $purchase->attachment = fileUploader($request->attachment, getFilePath("purchase_attachment"));
                } catch (\Exception $exp) {
                    $notify[] = 'Couldn\'t upload your attachment';
                    return jsonResponse('exception', 'error', $notify);
                }
            }
            $purchase->save();


            $purchaseDetails = [];

            foreach ($request->purchase_details as $requestPurchaseDetail) {

                //make product details
                $requestProductDetails = makeProductDetails($requestPurchaseDetail);

                $productDetail = ProductDetail::where('id', $requestPurchaseDetail['product_details_id'])->first();

                //update product stock and product details
                if ($request->status == Status::PURCHASE_RECEIVED) {
                    $productDetail->update($requestProductDetails);
                    //update product stock
                    $this->updateStock($purchase, $productDetail, $requestPurchaseDetail['qty']);
                }

                $purchaseDetails[]       = array_merge($requestProductDetails, [
                    'product_id'         => $productDetail->product_id,
                    'product_details_id' => $productDetail->id,
                    'purchase_id'        => $purchase->id,
                    'quantity'           => $requestPurchaseDetail['qty'],
                ]);
            }


            PurchaseDetails::insert($purchaseDetails);

            //supplier payment
            if ($request->paid_amount > 0) {
                $this->purchasePayment($purchase, $request);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $notify[] = $e->getMessage();
            // adminActivity("purchase", activityMessage: "Try the purchase add but failed for: " . $e->getMessage());
            return jsonResponse('exception', 'error', $notify);
        }

        // adminActivity("purchase-add", get_class($purchase), $purchase->id);
        $message[] = "Purchase added successfully";
        return jsonResponse('purchase', 'success', $message);
    }

    public function update(Request $request, $id) {
        $validator = $this->validation($request, $id);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $purchase = Purchase::find($id);
        if (!$purchase) {
            $notify[] = "The purchase is not found";
            return jsonResponse('validation_error', 'error', $notify);
        }

        if ($request->status && $purchase->status == Status::PURCHASE_RECEIVED && $request->status != Status::PURCHASE_RECEIVED) {
            $notify[] = "You cannot change status, if it's already Received";
            return jsonResponse('status', 'error', $notify);
        }


        $shippingAmount = $request->shipping_amount ?? 0;

        //get the sub total
        $subtotal       = array_reduce($request->purchase_details, function ($carry, $productDetail) {
            return $carry + $productDetail['qty'] * $productDetail['purchase_price'];
        }, 0);

        $discountAmount = 0;
        $discountValue  = $request->discount ?? 0;
        $discountType   = $request->discount_type ?? 0;

        if ($discountValue > 0) {
            if ($discountType == Status::DISCOUNT_PERCENT) {
                $discountAmount = $subtotal * $discountValue / 100;
            } else {
                $discountAmount = $discountValue;
            }
        }

        if ($discountAmount > $subtotal) {
            $notify[] = "The discount amount can not be greater than total";
            return jsonResponse('limit', 'error', $notify);
        }

        $total = $subtotal - $discountAmount + $shippingAmount;



        try {
            DB::beginTransaction();
            $purchase->purchase_date    = now()->parse($request->purchase_date)->format('Y-m-d');
            $purchase->reference_number = $request->reference_no ?? null;

            $purchase->discount_type   = $discountType;
            $purchase->discount_value  = $discountValue;
            $purchase->discount_amount = $discountAmount;

            $purchase->shipping_amount = $shippingAmount;
            $purchase->subtotal        = $subtotal;
            $purchase->total           = $total;
            $purchase->status          = $request->status;

            if ($request->hasFile('attachment')) {
                try {
                    $purchase->attachment = fileUploader($request->attachment, getFilePath("purchase_attachment"));
                } catch (\Exception $exp) {
                    $notify[] = 'Couldn\'t upload your attachment';
                    return jsonResponse('exception', 'error', $notify);
                }
            }
            $purchase->save();


            $purchaseDetails    = [];
            $isPurchaseReceived = $request->status == Status::PURCHASE_RECEIVED;

            foreach ($request->purchase_details as $requestPurchaseDetail) {

                //update product details
                $requestProductDetails = makeProductDetails($requestPurchaseDetail);

                //update product details if purchase received
                $productDetail         = ProductDetail::where('id', $requestPurchaseDetail['product_details_id'])->first();
                if ($isPurchaseReceived) {
                    $productDetail->update($requestProductDetails);
                }

                $reqPurchaseQty      = $requestPurchaseDetail['qty'];

                if (array_key_exists('purchase_details_id', $requestPurchaseDetail)) {
                    $findPurchaseDetails = PurchaseDetails::find($requestPurchaseDetail['purchase_details_id']);
                    $purchaseQty         = $findPurchaseDetails->quantity;

                    $findPurchaseDetails->update(array_merge($requestProductDetails, [
                        'quantity' => $reqPurchaseQty,
                    ]));

                    //update stock if purchase received
                    if ($isPurchaseReceived && $purchaseQty != $reqPurchaseQty) {
                        if ($purchaseQty > $reqPurchaseQty) {
                            $qty        = $purchaseQty - $reqPurchaseQty;
                            $updateType = "-";
                        } else {
                            $qty        = $reqPurchaseQty - $purchaseQty;
                            $updateType = "+";
                        }
                        $this->updateStock($purchase, $productDetail, $qty, $updateType);
                    }
                } else {

                    //update product stock
                    if ($isPurchaseReceived) {
                        $this->updateStock($purchase, $productDetail, $reqPurchaseQty);
                    }

                    $purchaseDetails[]       = array_merge($requestProductDetails, [
                        'product_id'         => $productDetail->product_id,
                        'product_details_id' => $productDetail->id,
                        'purchase_id'        => $purchase->id,
                        'quantity'           => $reqPurchaseQty,
                    ]);
                }
            }

            PurchaseDetails::insert($purchaseDetails);

            //supplier payment
            if ($request->paid_amount > 0) {
                $this->purchasePayment($purchase, $request);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $notify[] = $e->getMessage();
            // adminActivity("purchase", get_class($purchase), $purchase->id, "Try the purchase update but failed for: " . $e->getMessage());
            return jsonResponse('exception', 'error', $notify);
        }

        // adminActivity("purchase-updated", get_class($purchase), $purchase->id);
        $message[] = "Purchase update successfully";
        return jsonResponse('purchase', 'success', $message);
    }

    public function addPayment(Request $request, $purchaseId) {
        $request->validate([
            'paid_amount'     => 'required|numeric|gt:0',
            'paid_date'       => 'required',
            'payment_type'    => 'required|integer|exists:payment_types,id',
            'payment_account' => 'required|integer|exists:payment_accounts,id',
            'payment_note'    => 'nullable|string',
        ]);

        $purchase  = Purchase::where('id', $purchaseId)->withSum('supplierPayments', 'amount')->firstOrFailWithApi("purchase");
        $dueAmount = $purchase->total - $purchase->supplier_payments_sum_amount;

        if ($dueAmount <= 0) {
            $notify = "You cannot make more payments against this purchase";
            return responseManager("limitation", $notify);
        }
        if ($request->paid_amount > $dueAmount) {
            $notify = "Maximum add payment amount is " . showAmount($dueAmount);
            return responseManager("limitation", $notify);
        }

        $this->purchasePayment($purchase, $request);
        // adminActivity("purchase-payment", get_class($purchase), $purchase->id);
        $notify =  "Purchase payment added successfully";
        return responseManager("purchase_payment", $notify, 'success');
    }


    public function updateStatus(Request $request, $id) {
        $request->validate([
            'status' => ['required', Rule::in(Status::PURCHASE_ORDERED, Status::PURCHASE_RECEIVED, Status::PURCHASE_PENDING)]
        ]);

        $purchase = Purchase::where('id', $id)->firstOrFailWithApi("purchase");

        if ($purchase->status == Status::PURCHASE_RECEIVED) {
            $notify = "You cannot change status, if it's already Received";
            return responseManager("limitation", $notify);
        }

        if ($purchase->status == $request->status) {
            $notify = "Please change the status of the update";
            return responseManager("limitation", $notify);
        }

        try {
            DB::beginTransaction();
            $purchase->status = $request->status;
            $purchase->save();

            //update stock  if purchase received
            if ($request->status == Status::PURCHASE_RECEIVED) {
                foreach ($purchase->purchaseDetails as $purchaseDetails) {

                    $productDetail        = $purchaseDetails->productDetail;
                    $purchaseDetailsArray = $purchaseDetails->toArray();

                    $productDetail->update(makeProductDetails(array_merge($purchaseDetailsArray, [
                        'discount' => $purchaseDetailsArray['discount_value']
                    ])));
                    $this->updateStock($purchase, $productDetail, $purchaseDetails->quantity);
                }
            }
            // adminActivity("purchase-status-change", get_class($purchase), $purchase->id);
            DB::commit();
            $notify =  "Purchase status updated successfully";
            return responseManager("change_status", $notify, 'success');
        } catch (Exception $e) {
            DB::rollBack();
            $notify[] = $e->getMessage();
            // adminActivity("purchase", get_class($purchase), $purchase->id, "Try the update purchase status but failed for: " . $e->getMessage());
            return jsonResponse('exception', 'error', $notify);
        }
    }

    public function pdf($id) {
        $pageTitle = "Purchase Invoice";
        $purchase  = Purchase::with("warehouse", "supplier")
            ->withSum('supplierPayments', 'amount')
            ->where("id", $id)
            ->firstOrFailWithApi("purchase");


        $pdf      = Pdf::loadView('Template::user.purchase.pdf', compact('purchase', 'pageTitle'));
        $fileName = "Purchase Invoice - " . $purchase->invoice_number . ".pdf";
        return $pdf->download($fileName);
    }


    public function removeSingleItem($id) {
        $purchaseItem = PurchaseDetails::where("id", $id)->firstOrFailWithApi("PurchaseDetails");
        $purchase     = Purchase::where('id', $purchaseItem->purchase_id)->first();

        if (PurchaseDetails::where('purchase_id', $purchase->id)->count() <= 1) {
            return responseManager("error", "At least one item is required to perform this action.", 'error');
        }

        //adjust stock
        if ($purchase->status == Status::PURCHASE_RECEIVED) {
            $findStock = ProductStock::where('product_id', $purchaseItem->product_id)
                ->where('product_details_id', $purchaseItem->product_details_id)
                ->where('warehouse_id', $purchase->warehouse_id)
                ->first();
            $findStock->stock -= $purchaseItem->quantity;
            $findStock->save();
        }

        $purchaseItem->delete();
        return responseManager("success", "Purchase Item deleted successfully.", 'success');
    }

    private function basicDataForPurchase() {
        return [
            'warehouses'     => Warehouse::where('user_id', auth()->id())->active()->get(),
            'taxes'          => Tax::where('user_id', auth()->id())->active()->get(),
            'paymentMethods' => PaymentType::where('user_id', auth()->id())->active()->with('paymentAccounts', function ($q) {
                $q->active();
            })->get(),
            'suppliers' => Supplier::where('user_id', auth()->id())->active()->get()
        ];
    }

    private function validation($request, $id = 0) {
        $isRequired = $id ? 'nullable' : 'required';

        $validator = Validator::make($request->all(), [
            'purchase_date'    => 'required|date',
            'supplier_id'      => "$isRequired|integer|exists:suppliers,id",
            'warehouse_id'     => "$isRequired|integer|exists:warehouses,id",
            'reference_number' => 'nullable|string|max:255',
            'status'           => ['required', 'integer', Rule::in(Status::PURCHASE_ORDERED, Status::PURCHASE_PENDING, Status::PURCHASE_RECEIVED)],

            'discount_type'   => ['nullable', 'integer', Rule::in(Status::DISCOUNT_PERCENT, Status::DISCOUNT_FIXED)],
            'discount_value'  => 'nullable|numeric|gte:0',
            'shipping_amount' => 'nullable|numeric|gte:0',

            'paid_amount'     => 'nullable|numeric|gte:0',
            'paid_date'       => 'required_with:paid_amount',
            'payment_type'    => 'required_with:paid_amount',
            'payment_account' => 'required_with:paid_amount',
            'payment_note'    => 'nullable|string',

            'purchase_details'                      => 'required|array|min:1',
            "purchase_details.*.product_details_id" => "required|exists:product_details,id",
            "purchase_details.*.qty"                => "required|numeric|gt:0",
            "purchase_details.*.base_price"         => "required|numeric|gt:0",
            "purchase_details.*.tax_id"             => "nullable|integer|exists:taxes,id",
            "purchase_details.*.tax_type"           => ["nullable", Rule::in(Status::TAX_TYPE_EXCLUSIVE, Status::TAX_TYPE_INCLUSIVE)],
            "purchase_details.*.purchase_price"     => "required|numeric|gt:0",
            "purchase_details.*.sale_price"         => "required|numeric|gt:0",
            "purchase_details.*.profit_margin"      => "required|numeric|gte:0",
            "purchase_details.*.discount_type"      => ["nullable", Rule::in(Status::DISCOUNT_PERCENT, Status::DISCOUNT_FIXED)],
            "purchase_details.*.discount_value"     => "nullable|numeric|gt:0",
        ], [
            'purchase_details.min'      => "At least one product is required",
            'purchase_details.required' => "At least one product is required"
        ]);

        return $validator;
    }

    private function purchasePayment($purchase, $request) {
        $paymentNote                         = "Paid to supplier paid amount on purchase - " . $purchase->reference_number;
        $supplierPayment                     = new SupplierPayment();
        $supplierPayment->purchase_id        = $purchase->id;
        $supplierPayment->supplier_id        = $purchase->supplier_id;
        $supplierPayment->amount             = $request->paid_amount;
        $supplierPayment->payment_note       = $request->payment_note ?? $paymentNote;
        $supplierPayment->payment_date       = now()->parse($request->paid_date)->format('Y-m-d');
        $supplierPayment->payment_type_id    = $request->payment_type;
        $supplierPayment->payment_account_id = $request->payment_account;
        $supplierPayment->save();


        $paymentAccount = PaymentAccount::where('id', $request->payment_account)->first();

        if ($paymentAccount) {
            $details = "The supplier paid amount subtract from the payment account. Purchase invoice number: #" . $purchase->invoice_number;
            createTransaction($paymentAccount, '-', $request->paid_amount, 'balance_subtract', $details);
        }
    }

    private function updateStock($purchase, $productDetail, $qty, $updateType = "+") {
        $stock = ProductStock::where('product_details_id', $productDetail->id)
            ->where('product_id', $productDetail->product_id)
            ->where('warehouse_id', $purchase->warehouse_id)
            ->first();

        if (!$stock) {
            $stock                     = new ProductStock();
            $stock->warehouse_id       = $purchase->warehouse_id;
            $stock->product_id         = $productDetail->product_id;
            $stock->product_details_id = $productDetail->id;
        }

        if ($updateType == "+") {
            $stock->stock += $qty;
        } else {
            $stock->stock -= $qty;
        }
        $stock->save();
    }

    private function invoiceNumber() {
        $purchaseId = Purchase::max('id') + 1;
        $prefix     = gs('prefix_setting');
        return $prefix->purchase_invoice_prefix . (1000 + $purchaseId);
    }


    public function downloadAttachment($id) {
        $pageTitle = "Purchase Attachment";
        $purchase  = Purchase::where("id", $id)->firstOrFailWithApi('Purchase');

        if (!$purchase->attachment) {
            return jsonResponse("not_found", "error", ['The attachment is not available']);
        }
        return responseManager("purchase_attachment", $pageTitle, 'success', [
            'url' => getImage(getFilePath('purchase_attachment') . '/' . $purchase->attachment)
        ]);
    }
}
