<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\Export\ExportManager;
use App\Lib\FileManager;
use App\Models\AdminActivity;
use App\Models\Cart;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\ProductDetail;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Tax;
use App\Models\Transaction;
use App\Notify\Notify;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Picqer\Barcode\Types\TypeCode128;

function systemDetails() {
    $system['name']                = 'ovosale';
    $system['web_version']         = '1.0';
    $system['admin_panel_version'] = '1.0.1';
    $system['mobile_app_version']  = '1.0';
    $system['android_version']     = '7.0';
    $system['ios_version']         = '16.0';
    $system['flutter_version']     = '3.27.2';

    return $system;
}

function slug($string) {
    return Str::slug($string);
}

function verificationCode($length) {
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8) {
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false) {
    $template = session('template') ?? gs('active_template');
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName() {
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null) {
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name);
}
function siteFavicon() {
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function loadReCaptcha() {
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003') {
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha() {
    return Captcha::verify();
}


function getTrx($length = 12) {
    $characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2) {
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = null, $separate = true, $exceptZeros = false, $currencyFormat = true, $separator = '') {
    if (!$decimal) {
        $decimal = gs('allow_precision');
    }


    if ($separate && !$separator) {
        $separator = str_replace(['space', 'none'], [' ', ''], gs('thousand_separator'));
    }

    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym') . $printAmount . ' ' . __(gs('cur_text'));
        } elseif (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return gs('cur_sym') . $printAmount;
        }
    }
    return $printAmount;
}


function removeElement($array, $value) {
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet) {
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text) {
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text) {
    return strtolower(str_replace(' ', '_', $text));
}


function strLimit($title = null, $length = 10) {
    return Str::limit($title, $length);
}


function getIpInfo() {
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}


function osBrowser() {
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}


function getTemplates() {
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website']      = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url                   = "#";
    $response              = CurlRequest::curlPostContent($url, $param);
    if ($response) {
        return $response;
    } else {
        return null;
    }
}


function getPageSections($arr = false) {
    $jsonUrl  = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image, $size = null, $isAvatar = false) {
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($isAvatar) {
        return asset('assets/images/avatar.jpg');
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}


function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null) {
    $globalShortCodes = [
        'site_name'       => gs('site_name'),
        'site_currency'   => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify               = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes   = $shortCodes;
    $notify->user         = $user;
    $notify->createLog    = $createLog;
    $notify->pushImage    = $pushImage;
    $notify->userColumn   = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null) {
    if (!$paginate) {
        $paginate = request()->paginate ??   gs('paginate_number');
    }
    return $paginate;
}

function getOrderBy($orderBy = null) {
    if (!$orderBy) {
        $orderBy = request()->order_by ?? 'desc';
    }
    return $orderBy;
}

function paginateLinks($data, $view = null) {
    $paginationHtml = $data->appends(request()->all())->links($view);
    echo '<div class="pagination-wrapper w-100">' . $paginationHtml . '</div>';
}


function menuActive($routeName, $param = null, $className = 'active') {

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) return $className;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) return $className;
            else return;
        }
        return $className;
    }
}


function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null) {
    $fileManager           = new FileManager($file);
    $fileManager->path     = $location;
    $fileManager->size     = $size;
    $fileManager->old      = $old;
    $fileManager->thumb    = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager() {
    return new FileManager();
}

function getFilePath($key) {
    return fileManager()->$key()->path;
}

function getFileSize($key) {
    return fileManager()->$key()->size;
}

function getFileExt($key) {
    return fileManager()->$key()->extensions;
}

function diffForHumans($date) {
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function checkSpecialRegex($string) {
    $regex = '/[+\-*\/%==!=<>]=?|&&|\|\||\.\.|::|->|@|\$|\^|~|\[|\]|\{|\}|\(|\)|;|,|=>|:]/';
    return preg_match($regex, $string);
}

function showDateTime($date, $format = null, $lang = null) {
    if (!$date) {
        return '-';
    }
    if (!$lang) {
        $lang = session()->get('lang');
        if (!$lang) {
            $lang = getDefaultLang();
        }
    }

    if (!$format) {
        $format = gs('date_format') . ' ' . gs('time_format');
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getDefaultLang() {
    return config('app.local') ?? 'en';
}


function verifyG2fa($user, $code, $secret = null) {
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode  = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false) {

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function urlPath($routeName, $routeParam = null) {
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path     = str_replace($basePath, '', $url);
    return $path;
}


function showMobileNumber($number) {
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email) {
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}


function getRealIP() {
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}


function appendQuery($key, $value) {
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b) {
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr) {
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null) {
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) return @$general->$key;
    return $general;
}
function isImage($string) {
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension     = pathinfo($string, PATHINFO_EXTENSION);
    return in_array($fileExtension, $allowedExtensions);
}

function isHtml($string) {
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}


function convertToReadableSize($size) {
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int)$matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}

function loadExtension($key) {
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}


function frontendImage($sectionName, $image, $size = null, $seo = false) {
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function jsonResponse(string $remark, string $status, array $message = [], array $data = [], $statusCode = 200): JsonResponse {
    $response = [
        'remark' => $remark,
        'status' => $status
    ];

    if (count($message)) $response['message'] = $message;
    if (count($data)) $response['data']       = $data;

    return response()->json($response, $statusCode);
}

function exportData($baseQuery, $exportType, $modelName, $printPageSize = "A4 portrait") {
    try {
        return (new ExportManager($baseQuery, $modelName, $exportType, $printPageSize))->export();
    } catch (Exception $ex) {
        $notify[] = ['error', $ex->getMessage()];
        return back()->withNotify($notify);
    }
}

function os(): array {
    return [
        'windows',
        'windows 10',
        'windows 7',
        'windows 8',
        'windows xp' . 'linux',
        'apple',
        'android',
        'ubuntu',
    ];
}

function supportedDateFormats(): array {
    return  [
        'Y-m-d',
        'd-m-Y',
        'd/m/Y',
        'm-d-Y',
        'm/d/Y',
        'D, M j, Y',
        'l, F j, Y',
        'F j, Y',
        'M j, Y'
    ];
}
function supportedTimeFormats(): array {
    return  [
        'H:i:s',
        'H:i',
        'h:i A',
        'g:i a',
        'g:i:s a'
    ];
}
function supportedThousandSeparator(): array {
    return  [
        ","     => "Comma",
        "."     => "Dot",
        "'"     => "Apostrophe",
        "space" => "Space",
        "none"  => "None",
    ];
}

function isApiRequest() {
    return request()->is('api/*');
}

function responseManager(string $remark, string $message, string $responseType = 'error', array $responseData = [], array $igNoreOnApi = []) {
    $isApi = isApiRequest();

    if ($isApi) {
        $notify[]     = $message;
        $ignoreForApi = array_merge($igNoreOnApi, ['view', 'pageTitle']);
        $responseData = array_diff_key(
            $responseData,
            array_flip($ignoreForApi)
        );
        return jsonResponse($remark, $responseType, $notify, $responseData);
    }

    if (array_key_exists('view', $responseData)) {
        return view($responseData['view'], $responseData);
    }

    $notify[] = [$responseType, $message];
    return back()->withNotify($notify);
}

function makeProductDetails($requestDetails) {

    $taxTypeExclusive    = Status::TAX_TYPE_EXCLUSIVE;
    $discountTypePercent = Status::DISCOUNT_PERCENT;

    $basePrice     = $requestDetails['base_price'];
    $taxAmount     = 0;
    $taxPercentage = 0;
    $taxId         = 0;
    $taxType       = @$requestDetails['tax_type'] ?? 0;
    $profitMargin  = $requestDetails['profit_margin'];
    $discountType  = @$requestDetails['discount_type'] ?? 0;
    $discountValue = @$requestDetails['discount'] ?? 0;


    //calculate the tax  amount
    if (array_key_exists('tax_id', $requestDetails)) {
        $tax = Tax::active()->where('id', $requestDetails['tax_id'])->first();
        if ($tax) {
            $taxAmount     = $basePrice * $tax->percentage / 100;
            $taxPercentage = $tax->percentage;
            $taxId         = $tax->id;
        }
    }

    // Calculate profit and sale,purchase price
    if ($taxTypeExclusive == $taxType) {
        $purchasePrice = $basePrice + $taxAmount;
    } else {
        $purchasePrice = $basePrice;
    }

    $profitAmount = $purchasePrice / 100 * $profitMargin;
    $salePrice    = $purchasePrice + $profitAmount;
    // Calculate the discount amount based on the discount type
    $discountAmount = 0;
    if ($discountType == $discountTypePercent && $discountValue > 0) {
        if ($discountValue > 100) {
            $discountValue = 100;
        }
        $discountAmount = $salePrice / 100 * $discountValue;
    } else {
        $discountAmount = $discountValue;
    }

    if ($discountAmount >= $salePrice) {
        throw new \Exception("The discount amount can not be greater then or equal to sale price");
    }

    // Calculate final sale price
    $finalSalePrice = $salePrice - $discountAmount;

    return [
        'base_price'      => $basePrice,
        'purchase_price'  => $purchasePrice,
        'profit_margin'   => $profitMargin,
        'sale_price'      => $salePrice,
        'tax_id'          => $taxId,
        'tax_type'        => $taxType,
        'tax_amount'      => $taxAmount,
        'tax_percentage'  => $taxPercentage,
        'final_price'     => $finalSalePrice,
        'discount_value'  => $discountValue,
        'discount_amount' => $discountAmount,
        'discount_type'   => $discountType,
    ];
}

function adminActivity(string $remark, string $modelName = null,  $modelId = 0, string $activityMessage = null) {
    $isApi = isApiRequest();
    $admin = $isApi ? auth()->user() : auth('admin')->user();

    if (is_null($activityMessage)) {
        $activityMessage = 'The ' . strtolower(keyToTitle($remark)) . ' successfully';
    }

    $activity               = new  AdminActivity();
    $activity->admin_id     = $admin->id;
    $activity->remark       = strtolower($remark);
    $activity->activity     = $activityMessage;
    $activity->model_name   = $modelName;
    $activity->model_id     = $modelId;
    $activity->ip_address   = request()->ip();
    $activity->user_agent   = request()->userAgent();
    $activity->user_browser = osBrowser()['browser'];
    $activity->is_api       = $isApi;
    $activity->save();
}

function getAdmin($column = null) {
    $admin = isApiRequest() ? auth()->user() : auth('admin')->user();
    if (is_null($column)) return $admin;

    return $admin->$column;
}

function productTooltip() {
    return [
        'purchase_price'   => 'Base Price + Tax',
        'sale_price'       => 'Purchase Price + Profit Margin',
        'final_sale_price' => 'Sale Price - Discount'
    ];
}

function saleAndPurchaseDataForGraph($maxDate, $dataFormat = "Y-m-d") {
    $today            = Carbon::today();
    $saleQuery        = Sale::query();
    $purchaseQuery    = Purchase::query();

    $dates    = [];
    $sales    = [];
    $purchase = [];

    for ($i = 0; $i < $maxDate; $i++) {
        $date = $today->copy()->subDays($i)->format('Y-m-d');
        array_push($sales, getAmount((clone $saleQuery)->where('user_id', auth()->id())->whereDate('sale_date', $date)->sum('total')));
        array_push($purchase, getAmount((clone $purchaseQuery)->where('user_id', auth()->id())->whereDate('purchase_date', $date)->sum('total')));

        $formattedDate = now()->parse($date)->format($dataFormat);
        array_push($dates, $formattedDate);
    }
    return [
        'dates'    => $dates,
        'sales'    => $sales,
        'purchase' => $purchase,
    ];
}


function productForSales() {

    $search = request()->search;
    $baseQuery = ProductDetail::query();

    if ($search) {
        $search = "%$search%";
        $baseQuery->where('sku', request()->search)->orWhereHas('product', function ($q) use ($search) {
            $q->where('product_code', "like", $search)->orWhere('name', "like", $search);
        });
    }

    $baseQuery->whereHas('product', function ($q) {
        $q->active();
        if (request()->category_id) {
            $q->whereIn('category_id', explode(",", request()->category_id));
        }
        if (request()->brand_id) {
            $q->whereIn('brand_id', explode(",", request()->brand_id));
        }
    })->withSum(['productStock' => function ($q) {
        $q->where('warehouse_id', request()->warehouse_id);
    }], 'stock')
        ->orderBy('product_stock_sum_stock', 'desc')
        ->paginate(getPaginate());

    if (request()->change_warehouse_id == true && isApiRequest()) {
        Cart::where('admin_id', getAdmin('id'))->delete();
    }

    $productDetails = $baseQuery->latest('id')->paginate(getPaginate());
    $products       = formattedProductDetails($productDetails);

    return ['products' => $products, 'hasMore' => $productDetails->hasMorePages()];
}

function formattedProductDetails($productDetails) {
    $formattedProductDetails       = [];

    foreach ($productDetails as $productDetail) {
        $formattedProductDetails[] = [
            'id'             => $productDetail->id,
            'name'           => $productDetail->product->name,
            'sku'            => $productDetail->sku,
            'product_type'   => $productDetail->product->product_type,
            'image_src'      => $productDetail->product->image_src,
            'attribute_name' => @$productDetail->attribute->name,
            'variant_name'   => @$productDetail->variant->name,
            'in_stock'       => $productDetail->product_stock_sum_stock ?? 0,
            'unit_name'      => @$productDetail->product->unit->short_name,
            'price'          => $productDetail->final_price,
            'original'       => $productDetail
        ];
    }
    return $formattedProductDetails;
}

function createTransaction($paymentAccount, $trxType, $amount, $remark, $details, $trx = null) {
    if ($amount <= 0) return 0;
    if ($trxType == "+") {
        $paymentAccount->balance += $amount;
    } else {
        $paymentAccount->balance -= $amount;
    }

    $paymentAccount->save();

    if (is_null($trx)) {
        $trx = getTrx();
    }

    $transaction                     = new Transaction();
    $transaction->payment_account_id = $paymentAccount->id;
    $transaction->user_id            = auth()->id();
    $transaction->trx_type           = $trxType;
    $transaction->amount             = $amount;
    $transaction->post_balance       = $paymentAccount->balance;
    $transaction->trx                = $trx;
    $transaction->remark             = $remark;
    $transaction->details            = $details;
    $transaction->save();
}


function generateBarcodeHtml($code) {
    $barcode = (new TypeCode128())->getBarcode($code);

    $renderer = new Picqer\Barcode\Renderers\SvgRenderer();
    $renderer->setForegroundColor([0, 0, 0]);

    $renderer->setSvgType($renderer::TYPE_SVG_INLINE);
    $renderer->setSvgType($renderer::TYPE_SVG_STANDALONE);
    return $renderer->render($barcode, 180);
}

function showFrequency($frequency) {
    switch ($frequency) {
        case Status::DAILY:
            return __('Daily');
        case Status::WEEKLY:
            return __('Weekly');
        case Status::MONTHLY:
            return __('Monthly');
        case Status::HALF_YEARLY:
            return __('Half Yearly');
        case Status::YEARLY:
            return __('Yearly');
    }
}

function subscriptionEndDate($startDate, $frequency) {
    $start = Carbon::parse($startDate);
    if($frequency == Status::YEARLY) {
        return $start->copy()->addYear();
    }elseif($frequency == Status::HALF_YEARLY) {
        return $start->copy()->addMonths(6);
    }elseif($frequency == Status::MONTHLY) {
        return $start->copy()->addMonth();
    }elseif($frequency == Status::WEEKLY) {
        return $start->copy()->addWeek();
    }elseif($frequency == Status::DAILY) {
        return $start->copy()->addDay();
    }
}
