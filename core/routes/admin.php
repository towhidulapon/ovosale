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


    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('invoice-wise', 'invoiceWiseReport')->name('profit.invoice_wise')->middleware('permission:view profit loss report,admin');
        Route::get('product-wise', 'productWiseReport')->name('profit.product_wise')->middleware('permission:view profit loss report,admin');
        Route::get('sale', 'saleReport')->name('sale')->middleware('permission:view sale report,admin');
        Route::get('purchase', 'purchaseReport')->name('purchase')->middleware('permission:view purchase report,admin');
        Route::get('stock', 'stockReport')->name('stock')->middleware('permission:view stock report,admin');
        Route::get('expense', 'expenseReport')->name('expense')->middleware('permission:view expense report,admin');
        Route::get('notification/history', 'notificationHistory')->name('notification.history')->middleware('permission:notification setting,admin');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details')->middleware('permission:notification setting,admin');
        Route::get('transaction', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
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

    //warehouse
    Route::controller('WareHoseController')->name('warehouse.')->prefix('warehouse')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view warehouse,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add warehouse,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit warehouse,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit warehouse,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash warehouse,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //category
    Route::controller('CategoryController')->name('category.')->prefix('category')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view category,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add category,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit category,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit category,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash category,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //brand
    Route::controller('BrandController')->name('brand.')->prefix('brand')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view brand,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add brand,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit brand,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit brand,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash brand,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //expense
    Route::name('expense.')->prefix('expense')->group(function () {
        //expense category
        Route::controller('ExpenseCategoryController')->name('category.')->prefix("category")->group(function () {
            Route::get('list', 'list')->name('list')->middleware('permission:view expense category,admin');
            Route::post('create', 'save')->name('create')->middleware('permission:add expense category,admin');
            Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit expense category,admin');
            Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit expense category,admin');

            //trash
            Route::prefix('trash')->name('trash.')->group(function () {
                Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash expense category,admin');
                Route::post('restore/{id}', 'restoreTrash')->name('restore');
                Route::get('list', 'listTrash')->name('list');
            });
        });

        //expense
        Route::controller('ExpenseController')->group(function () {
            Route::get('list', 'list')->name('list')->middleware('permission:view expense,admin');
            Route::post('create', 'save')->name('create')->middleware('permission:add expense,admin');
            Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit expense,admin');
            Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit expense,admin');

            //trash
            Route::prefix('trash')->name('trash.')->group(function () {
                Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash expense,admin');
                Route::post('restore/{id}', 'restoreTrash')->name('restore');
                Route::get('list', 'listTrash')->name('list');
            });
        });
    });

    //unit
    Route::controller('UnitController')->name('unit.')->prefix('unit')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view unit,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add unit,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit unit,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit unit,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash unit,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //attribute
    Route::controller('AttributeController')->name('attribute.')->prefix('attribute')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view attribute,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add attribute,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit attribute,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit attribute,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash attribute,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //variant
    Route::controller('VariantController')->name('variant.')->prefix('variant')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view variant,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add variant,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit variant,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit variant,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash variant,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //tax
    Route::controller('TaxController')->name('tax.')->prefix('tax')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view tax,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add tax,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit tax,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit tax,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash tax,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //coupon
    Route::controller('CouponController')->name('coupon.')->prefix('coupon')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view coupon,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add coupon,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit coupon,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit coupon,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash coupon,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //Payment type
    Route::controller('PaymentTypeController')->name('payment.type.')->prefix('payment-type')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view payment type,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add payment type,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit payment type,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit payment type,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash payment type,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //Payment account
    Route::controller('PaymentAccountController')->name('payment.account.')->prefix('payment-account')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view payment account,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add payment account,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit payment account,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit payment account,admin');
        Route::post('adjust-balance/{id}', 'adjustBalance')->name('adjust.balance')->middleware('permission:adjust payment account balance,admin');
        Route::post('transfer-balance/{id}', 'transferBalance')->name('transfer.balance')->middleware('permission:edit payment account,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash payment type,admin');
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

    //product
    Route::controller('ProductController')->name('product.')->prefix('product')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view product,admin');
        Route::get('create', 'create')->name('create')->middleware('permission:add product,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add product,admin');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('permission:edit product,admin');
        Route::get('view/{id}', 'view')->name('view')->middleware('permission:view product,admin');
        Route::post('update/{id}', 'update')->name('update')->middleware('permission:edit product,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit product,admin');
        Route::get('product-code-generate', 'generateProductCode')->name('code.generate');
        Route::get('print-label', 'printLabel')->name('print.label')->middleware('permission:print product barcode,admin');
        Route::get('search', 'search')->name('search');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash product,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });

    //purchase
    Route::controller('PurchaseController')->name('purchase.')->prefix('purchase')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view purchase,admin');
        Route::get('add', 'add')->name('add')->middleware('permission:add purchase,admin');
        Route::post('store', 'store')->name('store')->middleware('permission:add purchase,admin');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('permission:edit purchase,admin');
        Route::post('update/{id}', 'update')->name('update')->middleware('permission:edit purchase,admin');
        Route::post('ad/payment/{id}', 'addPayment')->name('ad.payment')->middleware('permission:add purchase payment,admin');
        Route::get('view/{id}', 'view')->name('view')->middleware('permission:view purchase,admin');
        Route::get('pdf/{id}', 'pdf')->name('pdf')->middleware('permission:download purchase invoice,admin');
        Route::get('print/{id}', 'print')->name('print');
        Route::post('update-status/{id}', 'updateStatus')->name('update.status')->middleware('permission:update purchase status,admin');
        Route::post('remove/single/item/{id}', 'removeSingleItem')->name('remove.single.item');
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


    // Stock Transfer

    Route::controller('StockTransferController')->name('stock.transfer.')->prefix('stock-transfer')->middleware("permission:view stock transfer,admin")->group(function () {
        Route::get('add', 'add')->name('add')->middleware("permission:add stock transfer,admin");
        Route::get('list', 'list')->name('list');
        Route::post('store', 'store')->name('store')->middleware("permission:add stock transfer,admin");
        Route::get('view/{id}', 'view')->name('view');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware("permission:edit stock transfer,admin");
        Route::post('update/{id}', 'update')->name('update')->middleware("permission:edit stock transfer,admin");
        Route::get('pdf/{id}', 'pdf')->name('pdf');
        Route::get('print/{id}', 'print')->name('print');
        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
    });


    // HRM Modules

    // Company
    Route::controller('CompanyController')->name('company.')->prefix('company')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view company,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add company,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit company,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit company,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash company,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });


    // Department
    Route::controller('DepartmentController')->name('department.')->prefix('department')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view department,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add department,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit department,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit department,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash department,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });


    // Designation
    Route::controller('DesignationController')->name('designation.')->prefix('designation')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view designation,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add designation,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit designation,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit designation,admin');
        Route::get('get-department/{companyId}', 'getDepartment')->name('get.departments');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash designation,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });


    // Shift
    Route::controller('ShiftController')->name('shift.')->prefix('shift')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view shift,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add shift,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit shift,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit shift,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash shift,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });


    // Employee
    Route::controller('EmployeeController')->name('employee.')->prefix('employee')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view employee,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add employee,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit employee,admin');
        Route::post('status-change/{id}', 'status')->name('status.change')->middleware('permission:edit employee,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash employee,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });


    // Leave Request
    Route::controller('LeaveController')->name('leave.')->prefix('leave')->group(function () {
        Route::get('request/list', 'list')->name('request.list')->middleware('permission:view leave request,admin');
        Route::post('request/create', 'save')->name('request.create')->middleware('permission:add leave request,admin');
        Route::post('request/update/{id}', 'save')->name('request.update')->middleware('permission:edit leave request,admin');

        //Request trash
        Route::prefix('request/trash')->name('request.trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash leave request,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });

        Route::get('type/list', 'typeList')->name('type.list')->middleware('permission:view leave type,admin');
        Route::post('type/create', 'typeSave')->name('type.create')->middleware('permission:add leave type,admin');
        Route::post('type/update/{id}', 'typeSave')->name('type.update')->middleware('permission:edit leave type,admin');
        Route::post('type-status-change/{id}', 'typeStatus')->name('type.status.change')->middleware('permission:edit leave type,admin');

        //Type trash
        Route::prefix('type/trash')->name('type.trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash leave type,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });


    // Attendance
    Route::controller('AttendanceController')->name('attendance.')->prefix('attendance')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view attendance,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add attendance,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit attendance,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash attendance,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });


    // Holidays
    Route::controller('HolidayController')->name('holiday.')->prefix('holiday')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view holiday,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add holiday,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit holiday,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash holiday,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
    });



    // Payroll
    Route::controller('PayrollController')->name('payroll.')->prefix('payroll')->group(function () {
        Route::get('list', 'list')->name('list')->middleware('permission:view payroll,admin');
        Route::post('create', 'save')->name('create')->middleware('permission:add payroll,admin');
        Route::post('update/{id}', 'save')->name('update')->middleware('permission:edit payroll,admin');

        //trash
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::post('temporary/{id}', 'temporaryTrash')->name('temporary')->middleware('permission:trash payroll,admin');
            Route::post('restore/{id}', 'restoreTrash')->name('restore');
            Route::get('list', 'listTrash')->name('list');
        });
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
