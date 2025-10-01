<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login');
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });
        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });
        Route::controller('ResetPasswordController')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change');
        });
    });
});


Route::middleware('admin')->group(function () {

    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all')->middleware('permission:view user,admin');
        Route::get('active', 'activeUsers')->name('active')->middleware('permission:view user,admin');
        Route::get('banned', 'bannedUsers')->name('banned')->middleware('permission:view user,admin');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified')->middleware('permission:view user,admin');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified')->middleware('permission:view user,admin');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified')->middleware('permission:view user,admin');
        Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified')->middleware('permission:view user,admin');
        Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending')->middleware('permission:view user,admin');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified')->middleware('permission:view user,admin');
        Route::get('with-balance', 'usersWithBalance')->name('with.balance')->middleware('permission:view user,admin');

        Route::get('detail/{id}', 'detail')->name('detail')->middleware('permission:view user,admin');
        Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details')->middleware('permission:view user,admin');
        Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve')->middleware('permission:update user,admin');
        Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject')->middleware('permission:update user,admin');
        Route::post('update/{id}', 'update')->name('update')->middleware('permission:update user,admin');
        Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance')->middleware('permission:update user,admin');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single')->middleware('permission:update user,admin');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single')->middleware('permission:update user,admin');
        Route::get('login/{id}', 'login')->name('login')->middleware('permission:update user,admin');
        Route::post('status/{id}', 'status')->name('status')->middleware('permission:update user,admin');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all')->middleware('permission:update user,admin');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send')->middleware('permission:update user,admin');
        Route::get('list', 'list')->name('list')->middleware('permission:view user,admin');
        Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count')->middleware('permission:view user,admin');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log')->middleware('permission:view user,admin');
    });

    Route::controller('AdminController')->group(function () {

        Route::get('dashboard', 'dashboard')->name('dashboard')->middleware('permission:view dashboard,admin');
        Route::get('chart/sales-purchase', 'saleAndPurchaseChart')->name('chart.sales.purchase');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile-update', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');
        Route::get('activity', 'activity')->name('activity');
        Route::get('low-stock-product', 'lowStockProduct')->name('low.stock.product');


        //Notification
        Route::get('notifications', 'notifications')->name('notifications')->middleware('permission:notification setting,admin');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read')->middleware('permission:notification setting,admin');;
        Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all')->middleware('permission:notification setting,admin');;
        Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all')->middleware('permission:notification setting,admin');;
        Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single')->middleware('permission:notification setting,admin');;

        //Report Bugs
        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

        Route::get('list', 'list')->name('list')->middleware('permission:view admin,admin');
        Route::post('store', 'save')->name('store')->middleware('permission:add admin,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit admin,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit admin,admin');
    });

    //customer
    Route::controller('CustomerController')->name('customer.')->prefix('customer')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view customer,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add customer,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit customer,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit customer,admin');
        Route::get('lazy-loading', 'lazyLoadingData')->name('lazy.loading');
        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash customer,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //supplier
    Route::controller('SupplierController')->name('supplier.')->prefix('supplier')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view supplier,admin');
        Route::get('view/{id}', 'view')->name('view')->middleware('permission:view supplier,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add supplier,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit supplier,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit supplier,admin');
        Route::get('lazy-loading', 'lazyLoadingData')->name('lazy.loading');
        Route::post('add-payment/{id}', 'addPayment')->name('add.payment')->middleware('permission:add purchase payment,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash supplier,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //role
    Route::controller('RoleController')->name('role.')->prefix('role')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view role,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add role,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit role,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit role,admin');
        Route::get('permission/{id}', 'permission')->name('permission')->middleware('permission:assign permission,admin');
        Route::post('permission/update/{id}', 'permissionUpdate')->name('permission.update')->middleware('permission:assign permission,admin');
    });


    //sale
    Route::controller('SaleController')->name('sale.')->prefix('sale')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view sale,admin');
        Route::get('add', 'add')->name('add')->middleware('permission:add sale,admin');
        Route::post('store', 'store')->name('store')->middleware('permission:add sale,admin');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('permission:edit sale,admin');
        Route::post('update/{id}', 'update')->name('update')->middleware('permission:edit sale,admin');
        Route::get('view/{id}', 'view')->name('view')->middleware('permission:view sale,admin');
        Route::get('pdf/{id}', 'pdf')->name('pdf')->middleware('permission:download sale invoice,admin');
        Route::get('print/{id}', 'print')->name('print');
        Route::post('remove/single/item/{id}', 'removeSingleItem')->name('remove.single.item');
        Route::post('apply/coupon', 'applyCoupon')->name('apply.coupon');
        Route::get('top-selling-product', 'topSellingProduct')->name('top.selling.product')->middleware('permission:view product,admin');
    });


    // extensions
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->middleware('permission:manage extension,admin')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });

    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->middleware('permission:manage language,admin')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}/{key}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });


    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->middleware('permission:notification setting,admin')->group(function () {
        //Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // SEO

    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->middleware('permission:application information,admin')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
    });
    Route::controller('GeneralSettingController')->group(function () {

        Route::get('system-setting', 'systemSetting')->name('setting.system');

        // General Setting
        Route::get('general-setting', 'general')->name('setting.general')->middleware('permission:general setting,admin');
        Route::post('general-setting', 'generalUpdate')->middleware('permission:general setting,admin');;

        // prefix Setting
        Route::get('prefix-setting', 'prefixSetting')->name('setting.prefix')->middleware('permission:prefix setting,admin');
        Route::post('prefix-setting', 'prefixSettingUpdate')->name('setting.prefix.update')->middleware('permission:prefix setting,admin');


        // company Setting
        Route::get('company-setting', 'companySetting')->name('setting.company')->middleware('permission:company setting,admin');
        Route::post('company-setting', 'companySettingUpdate')->name('setting.company.update')->middleware('permission:company setting,admin');

        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration')->middleware('permission:system configuration,admin');
        Route::get('setting/system-configuration/{key}', 'systemConfigurationUpdate')->name("setting.system.configuration.update")->middleware('permission:system configuration,admin');

        // Logo-Icon
        Route::get('setting/brand', 'logoIcon')->name('setting.brand')->middleware('permission:brand setting,admin');
        Route::post('setting/brand', 'logoIconUpdate')->name('setting.brand')->middleware('permission:brand setting,admin');

        // pwa icon
        Route::get('setting/pwa', 'pwaIcon')->name('setting.pwa')->middleware('permission:brand setting,admin');
        Route::post('setting/pwa', 'pwaIconUpdate')->name('setting.pwa')->middleware('permission:brand setting,admin');


        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');
    });
});
