<?php


use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');


Route::name('pos.')->prefix('pos')->group(function () {
    Route::controller('Pos\PosController')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('category', 'category')->name('category');
        Route::get('brand', 'brand')->name('brand');
        Route::get('product', 'product')->name('product');
        Route::get('pricing-details/{id}', 'productPricingDetails')->name('product.pricing.details');
    });
});


Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');

    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('/pwa-manifest', 'pwaManifest')->name('pwa.manifest');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blogs', 'blogs')->name('blogs');
    Route::get('blog/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');

    Route::get('/', function () {
        return to_route('admin.dashboard');
    })->name('home');
});
