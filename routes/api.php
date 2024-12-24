<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\MyGameController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SettingPageController;
use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;


Route::group(
    ['middleware' => ['ChangeLanguage']],
    function () {
        Route::post('verification-notification', [EmailVerificationController::class, 'verificationNotification']);
        Route::post('verify-code', [EmailVerificationController::class, 'verifyCode']);
        Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])->middleware('sanctum');

        Route::controller(AuthController::class)->group(function () {
            Route::post('/login', 'login');
            Route::get('/login/invitation', 'useInvitationCode');
            Route::get('getOtpForUser',  'getOtpForUser');
            Route::post('/social/register', 'socialRegister');
            Route::post('/register', 'register');
            Route::post('/logout', 'logout')->middleware('sanctum');
            Route::delete('delete-account', 'deleteAccount')->middleware('sanctum');
        });
        Route::controller(UserController::class)->group(function () {
            Route::post('/user-update', 'updateUserInfo')->middleware('sanctum');
        });
        Route::controller(SettingPageController::class)->group(function () {
            Route::get('terms', 'termsPage');
            Route::get('about', 'aboutPage');
            Route::get('privacy', 'privacyPage');
            Route::post('sendOtp', 'sendOtp')->name('sendOtp');
        });


        Route::controller(ContactUsController::class)->group(function () {
            Route::post('/contact-us', 'store');
        });

        Route::controller(NotificationController::class)->group(function () {
            Route::get('/test-notification', 'sendNotficationTest')->middleware('sanctum');
            Route::get('/notifications', 'getUserNotifications')->middleware('sanctum');
            Route::get('/notifications/unread', 'getUnReadNotifications')->middleware('sanctum');
            Route::post('/notifications/{notification}/read', 'markAsRead')->middleware('sanctum');
            Route::post('/notifications/mark-all-read', 'markAllAsRead')->middleware('sanctum');
            Route::delete('/notifications/{notificationId}/delete',  'deleteNotification')->middleware('sanctum');
            Route::delete('/notifications/delete-all', 'deleteAllNotifications')->middleware('sanctum');
        });

        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index');
        });
        Route::prefix('medicines')->group(function () {
            Route::post('/store', [MedicineController::class, 'store']);
            Route::post('/update/{medicine}', [MedicineController::class, 'update']);
            Route::get('/', [MedicineController::class, 'getAllMedicines']);
            Route::get('/expired', [MedicineController::class, 'getExpiredMedicines']);
            Route::get('/expiring-soon', [MedicineController::class, 'getMedicinesExpiringSoon']);
            Route::delete('/delete/{medicine}', [MedicineController::class, 'deleteMedicine']);
            Route::get('/shortcomings/{quantity}', [MedicineController::class, 'getShortcomings']);
            Route::post('/import', [MedicineController::class, 'import']);
        });
    },
);
