<?php

use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\BusinessTypesController;
use App\Http\Controllers\Admin\FacilitiesController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use App\Http\Controllers\Admin\VendorsController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessLanguageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\NewsSourceController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth', 'role:super-admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
   
    Route::prefix('business')->group(function() {
        Route::get('language-row/{index}', [BusinessController::class, 'getLanguageRow']);
        Route::get('destination-row/{index}', [BusinessController::class, 'getDestinationRow']);
    });
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UsersController::class);
    Route::post('users/{user}/reset-password', [UsersController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('businessTypes', BusinessTypesController::class);
    Route::resource('facilities', FacilitiesController::class);
    Route::get('settings', [BusinessSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [BusinessSettingController::class, 'update'])->name('settings.update');
    Route::match(['get', 'post'], 'templates/global', [NotificationTemplateController::class, 'global'])->name('templates.global');
    Route::resource('templates', NotificationTemplateController::class);
    
    // Vendors
    Route::post('vendors/{vendor}/regenerate-api-key', [VendorsController::class, 'regenerateApiKey'])->name('vendors.regenerate-api-key');
    Route::resource('vendors', VendorsController::class);
    
    // News Sources
    Route::resource('news-sources', NewsSourceController::class);
    
    // News Categories
    Route::resource('news-categories', NewsCategoryController::class);
    Route::resource('languages', BusinessLanguageController::class);
    // News Items - Special Routes First
    Route::get('news/search-tags', [NewsController::class, 'searchTags'])->name('news.search-tags');
    Route::post('news/upload-image', [NewsController::class, 'uploadImage'])->name('news.upload-image');
    Route::post('news/fetch', [NewsController::class, 'fetch'])->name('news.fetch');
    
    // News Items - Resource Routes
    Route::get('news/{news}/manage-related', [NewsController::class, 'manageRelated'])->name('news.manage-related');
    Route::post('news/{news}/related/{related}', [NewsController::class, 'addRelated'])->name('news.add-related');
    Route::delete('news/{news}/related/{related}', [NewsController::class, 'removeRelated'])->name('news.remove-related');
    Route::post('news/{news}/promote', [NewsController::class, 'promoteToMain'])->name('news.promote-to-main');    
    Route::patch('news/{news}/reject', [NewsController::class, 'reject'])->name('news.reject');
    Route::patch('news/{news}/activate', [NewsController::class, 'activate'])->name('news.activate');
    // Main Resource Route Last
    Route::resource('news', NewsController::class);
});
