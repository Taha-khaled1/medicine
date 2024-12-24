<?php

use App\Http\Controllers\Dashboard\BannerController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ContactUsController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PaymentGatewayController;
use App\Http\Controllers\Dashboard\ProfessionController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\ServiceClassificationController;
use App\Http\Controllers\Dashboard\SettingWebController;
use App\Http\Controllers\Dashboard\SkillController;
use App\Http\Controllers\Dashboard\UserTransactionController;
use App\Http\Controllers\Dashboard\VendorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        if (Auth::User()->hasRole('admin')) {
            return redirect()->route('home')->with('success', 'successfully');
        } else if (Auth::User()->hasRole('vendor')) {
            return redirect()->route('vendorMain')->with('success', 'successfully');
        } else {
            return 'user';
        }
    })->middleware('vendorshop');
    Route::get('/orders-statistics', [DashboardController::class, 'getStatistics']);
    Route::get('/home', [DashboardController::class, 'main'])->name('home');
    Route::get('/vendor', [VendorController::class, 'vendorMain'])->name('vendorMain')->middleware('vendorshop');
    Route::resource('payment_gateways', PaymentGatewayController::class);

    Route::resource('roles', RoleController::class);
    Route::post('/payment_gateways/update-status', [PaymentGatewayController::class, 'updateStatusCatogery'])->name('payment_gateways.update-status');

    Route::get('/notification/markAllAsRead', [NotificationController::class, 'markAllAsRead'])->name('notification.markAllAsRead');
    Route::controller(ContactUsController::class)->group(function () {
        Route::get('/contactus', 'index')->name('contactus');

        Route::delete('/contactus/destroy', 'destroy')->name('contactus.destroy');
    });

    Route::controller(CouponController::class)->group(function () {
        Route::get('/coupons', 'index')->name('coupons');
        Route::get('/coupons/create', 'create')->name('coupons.create');
        Route::post('/coupons/store', 'store')->name('coupons.store');
        Route::post('/coupons/update', 'update')->name('coupons.update');
        Route::post('/coupons/destroy', 'destroy')->name('coupons.destroy');
    });
    Route::resource('professions', ProfessionController::class);
    Route::resource('transaction', UserTransactionController::class);
    Route::resource('service_classifications', ServiceClassificationController::class);
    Route::resource('skills', SkillController::class);
    Route::controller(SettingController::class)->group(function () {
        Route::get('/setting', 'index')->name('setting')->middleware('vendorshop');;
        Route::post('/setting.store', 'store')->name('setting.store');
        Route::post('/setting.update', 'update')->name('setting.update');
        Route::post('/setting.destroy', 'destroy')->name('setting.destroy');
    });
    Route::controller(BannerController::class)->group(function () {
        Route::get('/banners', 'index')->name('banners');
        // Route::get('/coupons/create', 'create')->name('coupons.create');
        Route::post('/banners/store', 'store')->name('banners.store');
        Route::put('/banners/update', 'update')->name('banners.update');
        Route::delete('/banners/destroy', 'destroy')->name('banners.destroy');
        Route::post('/banners/update-status', 'updateStatusBanner')->name('banners.update-status');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'index')->name('user');
        Route::post('/user.store', 'store')->name('user.store');
        Route::post('/user.edit', 'edit')->name('user.edit');
        Route::post('/user.update', 'update')->name('user.update');
        Route::post('/user/update/note', 'userUpdateNote')->name('user.updateNote');
        Route::post('/user.destroy', 'destroy')->name('user.destroy');
        Route::post('/user/wallet', 'chargeWallet')->name('user.wallet');
        Route::post('/userCreate', 'create')->name('userCreate');
        Route::get('/userUpdate/{id}', 'userUpdate')->name('userUpdate');
        Route::get('/user/vendeors', 'vendeors')->name('user.vendeors');
    });

    Route::controller(SettingWebController::class)->group(function () {
        Route::get('/setting_web', 'index')->name('setting_web');
        Route::get('/setting/gift', 'gift')->name('setting.gift');
        Route::get('/colorweb', 'colorweb')->name('colorweb');
        Route::post('/settings/update', 'update')->name('settings.update');
        Route::post('/settings/updateGift', 'updateGift')->name('settings.updateGift');
        Route::post('/settings/store', 'store')->name('settings.store');
        Route::post('updatewebsite', 'updatewebsite')->name('admin.updatewebsite');
    });
    Route::controller(PaymentGatewayController::class)->group(function () {
        Route::get('/gateways', 'index')->name('gateways');
        Route::post('/gateways/update', 'update')->name('gateways.update');
    });
});
