<?php

use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\BusinessTypesController;
use App\Http\Controllers\Admin\FacilitiesController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'role:super-admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UsersController::class);
    Route::resource('businessTypes', BusinessTypesController::class);
    Route::resource('facilities', FacilitiesController::class);
    Route::get('settings', [BusinessSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [BusinessSettingController::class, 'update'])->name('settings.update');
    Route::match(['get', 'post'], 'templates/global', [NotificationTemplateController::class, 'global'])->name('templates.global');
    Route::resource('templates', NotificationTemplateController::class);
});
