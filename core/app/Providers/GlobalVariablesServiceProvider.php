<?php

namespace App\Providers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Language;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class GlobalVariablesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $viewShare['emptyMessage'] = 'No data found';

        view()->composer(['admin.partials.topnav', "Template::partials.header", "Template::partials.auth_header"], function ($view) {
            $view->with([
                'languages' => Language::get()
            ]);
        });
        view()->composer(['components.permission_check', 'admin.partials.topnav',], function ($view) {
            $view->with([
                'admin' => Auth::guard('admin')->user()
            ]);
        });

        view()->composer(['admin.partials.sidenav', 'admin.partials.topnav'], function ($view) {
            $view->with([
                'menus' => json_decode(file_get_contents(resource_path('views/admin/partials/menu.json'))),
            ]);
        });

        view()->composer(['Template::partials.sidenav'], function ($view) {
            $view->with([
                'menus' => json_decode(file_get_contents(resource_path('views/templates/basic/partials/menu.json'))),
            ]);
        });

        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount'  => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount' => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'    => User::kycUnverified()->count(),
                'kycPendingUsersCount'       => User::kycPending()->count(),
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications'     => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
                'hasNotification'        => AdminNotification::exists(),
            ]);
        });



        view()->share($viewShare);
    }
}
