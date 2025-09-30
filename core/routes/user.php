<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //KYC
                Route::get('kyc-form', 'kycForm')->name('kyc.form');
                Route::get('kyc-data', 'kycData')->name('kyc.data');
                Route::post('kyc-submit', 'kycSubmit')->name('kyc.submit');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            //sale
            Route::controller('SaleController')->name('sale.')->prefix('sale')->group(function () {
                Route::get('list', 'list')->name('list');
                Route::get('add', 'add')->name('add');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::get('view/{id}', 'view')->name('view');
                Route::get('pdf/{id}', 'pdf')->name('pdf');
                Route::get('print/{id}', 'print')->name('print');
                Route::post('remove/single/item/{id}', 'removeSingleItem')->name('remove.single.item');
                Route::post('apply/coupon', 'applyCoupon')->name('apply.coupon');
                Route::get('top-selling-product', 'topSellingProduct')->name('top.selling.product');
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

            //Report

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



            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::middleware('kyc')->group(function () {
                    Route::get('/', 'withdrawMoney');
                    Route::post('/', 'withdrawStore')->name('.money');
                    Route::get('preview', 'withdrawPreview')->name('.preview');
                    Route::post('preview', 'withdrawSubmit')->name('.submit');
                });
                Route::get('history', 'withdrawLog')->name('.history');
            });
        });

        // Payment
        Route::prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
