<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'role:super-admin'], 'prefix' => 'admin' , 'as' => 'admin.', 'namespace' => 'Admin'], function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});