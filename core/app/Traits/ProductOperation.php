<?php

namespace App\Traits;

use App\Constants\Status;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Tax;
use App\Models\Unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait ProductOperation
{
    public function list()
    {
        $pageTitle = "Manage Product";
        $view      = "Template::user.product.list";
        $baseQuery = Product::where('user_id', auth()->id())->orderBy('id', 'desc')
            ->trashFilter()
            ->with(['details:id,product_id,final_price', 'category:id,name', 'brand:id,name'])
            ->searchable(['product_code', 'name', "details:sku", 'category:name', 'brand:name', 'unit:name']);

        if (request()->export) {
            return exportData($baseQuery, request()->export, "Product");
        }

        $products = $baseQuery->paginate(getPaginate());
        return responseManager("products", $pageTitle, 'success', compact('products', 'view', 'pageTitle'));
    }

    public function create()
    {
        $pageTitle = "Add Product";
        $view      = "Template::user.product.add";
        extract($this->basicDataForProductOperation());
        return responseManager("add_product", $pageTitle, 'success', compact('pageTitle', 'categories', 'units', 'brands', 'taxes', 'attributes', 'view'));
    }

    public function edit($id)
    {
        $pageTitle = "Edit Product";
        $view      = "Template::user.product.edit";
        $product   = Product::with('details.attribute', 'details.variant', 'details', 'details.tax')->where('id', $id)->firstOrFailWithApi('product');
        extract($this->basicDataForProductOperation());
        return responseManager("edit_product", $pageTitle, 'success', compact('pageTitle', 'categories', 'units', 'brands', 'taxes', 'attributes', 'view', 'product'));
    }

    public function view($id)
    {
        $pageTitle = "View Product";
        $view      = "Template::user.product.view";
        $product   = Product::with('details.attribute', 'details.variant', 'details', 'details.tax', 'category', 'brand', 'unit')->where('id', $id)->firstOrFailWithApi('product');
        return responseManager("view_product", $pageTitle, 'success', compact('pageTitle', 'product', 'view'));
    }

    public function search()
    {
        $search      = request()->search;
        $search      = "%$search%";
        $searchQuery = ProductDetail::where('sku', request()->search);
        $exactMatch  = true;

        if (!(clone $searchQuery)->count()) {
            $searchQuery->orWhereHas('product', function ($q) use ($search) {
                $q->where('sku', "like", $search)
                    ->orWhere('product_code', "like", $search)
                    ->orWhere('name', "like", $search);
            });
            $exactMatch  = false;
        }

        if (request()->warehouse_id) {
            $searchQuery->withSum(['productStock' => function ($q) {
                $q->where('warehouse_id', request()->warehouse_id);
            }], 'stock');
        }

        $productDetails = $searchQuery->with([
            'product',
            'attribute',
            'variant',
        ])->take(20)->get();


        $products  = formattedProductDetails($productDetails);
        $message[] = "product search results";

        return jsonResponse('product_search', 'success', $message, [
            'products'    => $products,
            'exact_match' => $exactMatch
        ]);
    }

    public function generateProductCode()
    {
        $code      = $this->getProductCode();
        $message[] = "Auto generate product code";
        return jsonResponse('code', 'success', $message, [
            'code' => $code
        ]);
    }

    private function getProductCode()
    {
        $maxId  = Product::max('id') + 1;
        $prefix = gs('prefix_setting');
        return $prefix->product_code_prefix . (1000 + $maxId);
    }
    public function status($id)
    {
        return Product::changeStatus($id);
    }

    public function save(Request $request)
    {

        $validator = $this->validation($request);
        $user = auth()->user();

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        $productCode = $request->product_code ?? $this->getProductCode();
        try {

            DB::beginTransaction();

            $product               = new Product();
            $product->user_id      = $user->id;
            $product->name         = $request->name;
            $product->product_code = $productCode;
            $product->product_type = $request->product_type;
            $product->category_id  = $request->category_id;
            $product->unit_id      = $request->unit_id;
            $product->brand_id     = $request->brand_id;
            $product->description  = $request->description ?? null;

            if ($request->hasFile('image')) {
                try {
                    $path           = getFilePath('product') . "/" . $productCode;
                    $product->image = fileUploader($request->image, $path);
                } catch (\Exception $exp) {
                    $message[] = "Couldn\'t upload your image";
                    return jsonResponse('exception', 'error', $message);
                }
            }

            $product->save();

            $productDetails = [];

            foreach ($request->product_detail as $k => $detail) {
                $sku = $this->generateProductSku($detail, $product, $k + 1);
                $productDetails[] = array_merge(makeProductDetails($detail), [
                    'product_id'     => $product->id,
                    'variant_id'     => $detail['variant_id'] ?? 0,
                    'attribute_id'   => $detail['attribute_id'] ?? 0,
                    'alert_quantity' => $detail['alert_quantity'],
                    'sku'            => $sku,
                    'barcode_html'   => generateBarcodeHtml($sku),
                ]);
            }

            ProductDetail::insert($productDetails);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            $message[] = $ex->getMessage();
            adminActivity("product", get_class($product), $product->id, "Try the product add but failed for: " . $ex->getMessage());
            return jsonResponse('exception', 'error', $message);
        }

        adminActivity("product-add", get_class($product), $product->id);
        $message[] = "Product added successfully";
        return jsonResponse('product', 'success', $message);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->validation($request, $id);

        if ($validator->fails()) {
            return jsonResponse('validation_error', 'error', $validator->errors()->all());
        }

        try {
            DB::beginTransaction();
            $product = Product::find($id);
            if (!$product) {
                $message[] = "Product not found";
                return jsonResponse('not_found', 'error', $message);
            }
            $product->name        = $request->name;
            $product->category_id = $request->category_id;
            $product->unit_id     = $request->unit_id;
            $product->brand_id    = $request->brand_id;
            $product->description = $request->description ?? null;

            if ($request->hasFile('image')) {
                try {
                    $path           = getFilePath('product') . "/" . $product->product_code;
                    $product->image = fileUploader($request->image, $path, old: $product->image);
                } catch (\Exception $exp) {
                    $message[] = "Couldn\'t upload your image";
                    return jsonResponse('exception', 'error', $message);
                }
            }

            $product->save();
            $productDetails = [];

            foreach ($request->product_detail as $k => $detail) {
                $makeProductDetails = makeProductDetails($detail);
                if (array_key_exists('id', $detail)) {
                    $productDetail = ProductDetail::where('id', $detail['id'])->first();
                    if (!$productDetail) {
                        throw new Exception("The product is not found");
                    }
                    $productDetail->update(array_merge($makeProductDetails, ['alert_quantity' => $detail['alert_quantity'],]));
                } else {
                    $productDetails[] = array_merge($makeProductDetails, [
                        'product_id'     => $product->id,
                        'variant_id'     => $detail['variant_id'] ?? 0,
                        'attribute_id'   => $detail['attribute_id'] ?? 0,
                        'sku'            => $this->generateProductSku($detail, $product, $k + 1),
                        'alert_quantity' => $detail['alert_quantity'],
                    ]);
                }
            }

            ProductDetail::insert($productDetails);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            $message[] = $ex->getMessage();
            adminActivity("product", get_class($product), $product->id, "Try the product update but failed for: " . $ex->getMessage());
            return jsonResponse('exception', 'error', $message);
        }

        adminActivity("product-updated", get_class($product), $product->id);
        $message[] = "Product update successfully";
        return jsonResponse('product', 'success', $message);
    }

    private function validation($request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';

        $validator = Validator::make($request->all(), [
            'name'         => 'required|unique:products,name,' . $id,
            'user_id'      => "required|integer",
            'brand_id'     => "required|integer|exists:brands,id",
            'unit_id'      => "required|integer|exists:units,id",
            'category_id'  => "required|integer|exists:categories,id",
            'product_code' => "nullable|unique:products,product_code," . $id,
            "product_type" => [$isRequired, Rule::in(Status::PRODUCT_TYPE_STATIC, Status::PRODUCT_TYPE_VARIABLE)],
            'image'        => "nullable|image",
            'description'  => "nullable|string",

            'product_detail'                  => 'required|array|min:1',
            "product_detail.*.id"             => "nullable|exists:product_details,id",
            "product_detail.*.sku"            => "nullable|unique:product_details,sku",
            "product_detail.*.base_price"     => "required|numeric|gt:0",
            "product_detail.*.tax_id"         => "nullable|integer|exists:taxes,id",
            "product_detail.*.tax_type"       => ["nullable", Rule::in(Status::TAX_TYPE_EXCLUSIVE, Status::TAX_TYPE_INCLUSIVE)],
            "product_detail.*.purchase_price" => "required|numeric|gt:0",
            "product_detail.*.sale_price"     => "required|numeric|gt:0",
            "product_detail.*.profit_margin"  => "required|numeric|gte:0",
            "product_detail.*.discount_type"  => ["nullable", Rule::in(Status::DISCOUNT_PERCENT, Status::DISCOUNT_FIXED)],
            "product_detail.*.discount_value" => "nullable|numeric|gt:0",
            "product_detail.*.alert_quantity" => "required|numeric|gte:0",

            "product_detail.*.variant_id"   => "nullable|exists:variants,id",
            "product_detail.*.attribute_id" => "nullable|exists:attributes,id",
        ], [
            'product_detail.*.alert_quantity.required' => "All the alert quantity field is required"
        ]);

        return $validator;
    }

    private function basicDataForProductOperation()
    {
        return
            [
                'categories' => Category::active()->get(),
                'brands'     => Brand::active()->get(),
                'units'      => Unit::active()->get(),
                'taxes'      => Tax::active()->get(),
                'attributes' => Attribute::active()->with('variants', function ($q) {
                    $q->active();
                })->get()
            ];
    }


    private function generateProductSku($requestProductDetail, $product, $k)
    {
        if (array_key_exists('sku', $requestProductDetail) && !is_null($requestProductDetail['sku'])) {
            return $requestProductDetail['sku'];
        };

        if ($product->product_type == Status::PRODUCT_TYPE_STATIC) return $product->product_code;
        return $product->product_code . "-" . $k;
    }
}
