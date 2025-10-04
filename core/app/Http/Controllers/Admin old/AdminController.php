<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\DashBoardWidgetData;
use App\Models\Admin;
use App\Models\AdminActivity;
use App\Models\AdminNotification;
use App\Models\ProductDetail;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\Warehouse;
use App\Traits\AdminOperation;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use AdminOperation;

    public function dashboard()
    {
        $pageTitle          = 'Dashboard';
        $widget             = DashBoardWidgetData::getWidgetData();
        $topSellingProducts = SaleDetails::selectRaw('SUM(quantity) as total_quantity,product_details_id')
            ->groupBy('product_details_id')
            ->with('productDetail', 'productDetail.attribute', "productDetail.variant", 'productDetail.product.unit')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();

        $recentSales     = Sale::with('customer')->latest('id')->take(5)->get();
        $recentPurchases = Purchase::with('supplier')->latest('id')->take(5)->get();
        $warehouses      = Warehouse::get();

        return view('admin.dashboard', compact('pageTitle', 'widget', 'topSellingProducts', 'recentSales', 'recentPurchases', 'warehouses'));
    }

    public function profile()
    {
        $pageTitle = 'My Profile';
        $admin     = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }



    public function password()
    {
        $pageTitle = 'Change Password';
        $admin     = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }


    public function notifications()
    {
        $notifications   = AdminNotification::orderBy('id', 'desc')->selectRaw('*,DATE(created_at) as date')->with('user')->paginate(getPaginate());
        $hasUnread       = AdminNotification::where('is_read', Status::NO)->exists();
        $hasNotification = AdminNotification::exists();
        $pageTitle       = 'All Notifications';
        return view('admin.notifications', compact('pageTitle', 'notifications', 'hasUnread', 'hasNotification'));
    }


    public function notificationRead($id)
    {
        $notification          = AdminNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function readAllNotification()
    {
        AdminNotification::where('is_read', Status::NO)->update([
            'is_read' => Status::YES
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function deleteAllNotification()
    {
        AdminNotification::truncate();
        $notify[] = ['success', 'Notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteSingleNotification($id)
    {
        AdminNotification::where('id', $id)->delete();
        $notify[] = ['success', 'Notification deleted successfully'];
        return back()->withNotify($notify);
    }

    public function activity()
    {
        $authAdminId = getAdmin('id');

        if (request()->admin_id) {
            abort_if($authAdminId != Status::SUPPER_ADMIN_ID, 403);
            $adminId = request()->admin_id;
        } else {
            $adminId = $authAdminId;
        }

        $pageTitle  = 'Admin Activity';
        $activities = AdminActivity::with('admin')->latest()->where('admin_id', $adminId)->selectRaw('*,DATE(created_at) as date')->paginate(getPaginate());
        return view('admin.activity', compact('pageTitle', 'activities'));
    }

    public function downloadAttachment($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;

        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
}
