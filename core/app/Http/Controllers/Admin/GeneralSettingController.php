<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GeneralSettingController extends Controller {

    public function general() {
        $pageTitle       = 'General Setting';
        $timezones       = timezone_identifiers_list();
        $currentTimezone = array_search(config('app.timezone'), $timezones);
        return view('admin.setting.general', compact('pageTitle', 'timezones', 'currentTimezone'));
    }

    public function prefixSetting() {
        $pageTitle = 'Prefix Setting';
        return view('admin.setting.prefix', compact('pageTitle'));
    }
    public function companySetting() {
        $pageTitle = 'Company Setting';
        return view('admin.setting.company', compact('pageTitle'));
    }

    public function companySettingUpdate(Request $request) {
        $request->validate([
            'company_information'         => 'required|array',
            'company_information.name'    => 'required',
            'company_information.phone'   => 'required',
            'company_information.address' => 'required',
            'company_information.email'   => 'nullable|email',
        ]);

        $gs                      = gs();
        $gs->company_information = $request->company_information;
        $gs->save();

        $notify[] = ['success', 'Company information updated successfully'];
        adminActivity("company-information-updated", get_class($gs), $gs->id);
        return back()->withNotify($notify);
    }
    public function generalUpdate(Request $request) {
        $request->validate([
            'site_name'          => 'required|string|max:40',
            'cur_text'           => 'required|string|max:40',
            'cur_sym'            => 'required|string|max:40',
            'timezone'           => 'required|integer',
            'currency_format'    => 'required|in:1,2,3',
            'paginate_number'    => 'required|integer',
            'time_format'        => ['required', Rule::in(supportedTimeFormats())],
            'date_format'        => ['required', Rule::in(supportedDateFormats())],
            'thousand_separator' => ['required', Rule::in(array_keys(supportedThousandSeparator()))],
            'allow_precision'    => 'required|integer|gt:0|lte:8'
        ]);

        $timezones = timezone_identifiers_list();
        $timezone  = @$timezones[$request->timezone] ?? 'UTC';

        $general                     = gs();
        $general->site_name          = $request->site_name;
        $general->cur_text           = $request->cur_text;
        $general->cur_sym            = $request->cur_sym;
        $general->paginate_number    = $request->paginate_number;
        $general->currency_format    = $request->currency_format;
        $general->time_format        = $request->time_format;
        $general->date_format        = $request->date_format;
        $general->allow_precision    = $request->allow_precision;
        $general->thousand_separator = $request->thousand_separator;
        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content      = '<?php $timezone = "' . $timezone . '" ?>';
        file_put_contents($timezoneFile, $content);
        $notify[] = ['success', 'General setting updated successfully'];

        adminActivity("generate-setting-updated", get_class($general), $general->id);
        return back()->withNotify($notify);
    }

    public function prefixSettingUpdate(Request $request) {
        $request->validate([
            'product_code_prefix'           => 'required',
            'purchase_invoice_prefix'       => 'required',
            'sale_invoice_prefix'           => 'required',
            'stock_transfer_invoice_prefix' => 'required',
        ]);

        $prefixSetting = [
            'purchase_invoice_prefix'       => $request->purchase_invoice_prefix,
            'sale_invoice_prefix'           => $request->sale_invoice_prefix,
            'product_code_prefix'           => $request->product_code_prefix,
            'stock_transfer_invoice_prefix' => $request->stock_transfer_invoice_prefix,
        ];

        $general                 = gs();
        $general->prefix_setting = $prefixSetting;
        $general->save();

        adminActivity("prefix-setting-updated", get_class($general), $general->id);
        $notify[] = ['success', 'Prefix setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function systemConfiguration() {
        $pageTitle      = 'System Configuration';
        $configurations = json_decode(file_get_contents(resource_path('views/admin/setting/configuration.json')));
        return view('admin.setting.configuration', compact('pageTitle', 'configurations'));
    }


    public function systemConfigurationUpdate($key) {
        try {
            $general   = gs();
            $newStatus = !$general->$key;

            $general->$key = $newStatus;
            $general->save();

            return response()->json([
                'success'    => true,
                'new_status' => $newStatus
            ]);
            adminActivity("system-configuration-updated", get_class($general), $general->id);
        } catch (Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ]);
        }
    }


    public function logoIcon() {
        $pageTitle = 'Brand Setting';
        return view('admin.setting.logo_icon', compact('pageTitle'));
    }

    public function logoIconUpdate(Request $request) {
        $request->validate([
            'logo'    => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'favicon' => ['image', new FileTypeValidate(['png'])],
        ]);
        $path = getFilePath('logoIcon');

        if ($request->hasFile('logo')) {
            try {
                fileUploader($request->logo, $path, filename: 'logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }
        if ($request->hasFile('logo_dark')) {
            try {
                fileUploader($request->logo_dark, $path, filename: 'logo_dark.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                fileUploader($request->favicon, $path, filename: 'favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the favicon'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'Brand setting updated successfully'];
        return back()->withNotify($notify);
    }


    public function pwaIcon() {
        $pageTitle = 'PWA Setting';
        return view('admin.setting.pwa', compact('pageTitle'));
    }

    public function pwaIconUpdate(Request $request) {

        $request->validate([
            'pwa_small_icon' => ['image', new FileTypeValidate(['png']), "dimensions:width=192,height=192"],
            'pwa_large_icon' => ['image', new FileTypeValidate(['png']), "dimensions:width=512,height=512"],
        ]);

        $path = getFilePath('logoIcon');

        if ($request->hasFile('pwa_small_icon')) {
            try {
                fileUploader($request->pwa_small_icon, $path, filename: 'pwa_small_icon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the PWA small icon'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('pwa_large_icon')) {
            try {
                fileUploader($request->pwa_large_icon, $path, filename: 'pwa_large_icon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the PWA large icon'];
                return back()->withNotify($notify);
            }
        }
        $notify[] = ['success', 'PWA images updated successfully'];
        return back()->withNotify($notify);
    }
}
