<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\BusinessTypesController;
use App\Http\Controllers\Admin\FacilitiesController;
use App\Http\Controllers\BusinessSettingController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'role:super-admin'], 'prefix' => 'admin' , 'as' => 'admin.'], function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UsersController::class);
    Route::resource('businessTypes', BusinessTypesController::class);
    Route::resource('facilities', FacilitiesController::class);
    Route::resource('settings', BusinessSettingController::class);
});