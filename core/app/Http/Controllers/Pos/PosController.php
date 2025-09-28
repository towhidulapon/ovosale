<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\PaymentType;
use App\Models\ProductDetail;
use App\Models\Warehouse;


class PosController extends Controller
{
    public function index()
    {
        $pageTitle    = "Pos";
        $warehouses   = Warehouse::active()->get();
        $paymentTypes = PaymentType::active()->with('paymentAccounts', function ($q) {
            $q->active();
        })->orderBy('name')->get();
        return view('pos.index', compact('pageTitle', 'warehouses', 'paymentTypes'));
    }

    public function category()
    {
        $categories = Category::active()->get();
        $message[]  = "Category list";

        return jsonResponse('category', 'success', $message, [
            'categories' => $categories
        ]);
    }

    public function brand()
    {
        $brands    = Brand::active()->get();
        $message[] = "Brand list";

        return jsonResponse('brand', 'success', $message, [
            'brands' => $brands
        ]);
    }

    public function product()
    {
        $message[] = "Product List";
        extract(productForSales());
        return jsonResponse('product', 'success', $message, [
            'products' => $products,
            'has_more' => $hasMore
        ]);
    }

    public function productPricingDetails($id)
    {
        $product = ProductDetail::with('product')->find($id);

        if (!$product) {
            $message[] = "The product not found";
            return jsonResponse('not_found', 'error', $message);
        }

        $message[] = "Product pricing details";
        $html = view('pos.partials.pricing_details', compact('product'))->render();
        return jsonResponse('not_found', 'success', $message, [
            'html' => $html
        ]);
    }
}
