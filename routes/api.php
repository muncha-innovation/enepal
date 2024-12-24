<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuth\LoginController;
use App\Http\Controllers\ApiAuth\RegistrationController;
use App\Http\Controllers\ApiAuth\ResetPasswordController;
use App\Http\Controllers\APIS\BusinessController;
use App\Http\Controllers\APIS\BusinessTypesController;
use App\Http\Controllers\APIS\CountryController;
use App\Http\Controllers\APIS\GalleryController;
use App\Http\Controllers\APIS\NoticeController;
use App\Http\Controllers\APIS\NotificationController;
use App\Http\Controllers\APIS\PostsController;
use App\Http\Controllers\APIS\AddressPreferencesController;
use App\Http\Controllers\APIS\ProductsController;
use App\Http\Controllers\APIS\UsersController;
use App\Http\Controllers\APIS\NewsApiController;
use App\Http\Controllers\APIS\NewsRecommendationController;

Route::post('/login', LoginController::class);
Route::post('/register', RegistrationController::class);
Route::post('/password/reset', ResetPasswordController::class);
Route::get('/countries/{country}/states', [CountryController::class, 'states']);
Route::get('business/types', [BusinessTypesController::class, 'index']);

Route::get('post/{id}/comments', [PostsController::class, 'getComments']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UsersController::class, 'user']);
    Route::get('business/types/{id}', [BusinessTypesController::class, 'getById']);
    Route::get('posts/{id}', [PostsController::class, 'getById']);
    Route::get('business/{id}', [BusinessController::class, 'getById']);

    Route::get('posts', [PostsController::class, 'index']);

    Route::post('posts/{id}/like', [PostsController::class, 'likeUnlike']);
    Route::get('businesses', [BusinessController::class, 'getBusinesses']);
    Route::post('/comments/add', [PostsController::class, 'addComment']);
    
    Route::post('/fcm/update', [UsersController::class, 'updateFcmToken']);
    Route::post('/news-preferences/toggle/{category}', [UsersController::class, 'toggleNewsPreference']);

    Route::get('products', [ProductsController::class, 'index']);
    Route::get('products/{id}', [ProductsController::class, 'getById']);

    Route::get('galleries', [GalleryController::class, 'index']);
    Route::get('galleries/{id}', [GalleryController::class, 'getById']);

    Route::get('notices', [NoticeController::class, 'index']);
    Route::get('notices/{id}', [NoticeController::class, 'getById']);

    Route::get('notifications/user', [NotificationController::class, 'userNotifications']);
    Route::get('notifications/business', [NotificationController::class, 'businessNotifications']);
    // Route::
    Route::group(['prefix' => 'business'], function () {
        Route::get('user/following', [BusinessController::class, 'following']);
        Route::get('posts', [BusinessController::class, 'posts']);
        Route::get('products', [BusinessController::class, 'products']);
        Route::get('notices', [BusinessController::class, 'notices']);
        Route::get('galleries', [BusinessController::class, 'galeries']);

        Route::post('follow/{id}', [BusinessController::class, 'followUnfollow']);
    });
    Route::group(['prefix' => 'address/preferences'], function() {
        Route::get('fetch', [AddressPreferencesController::class, 'fetch']);
        Route::post('update', [AddressPreferencesController::class, 'update']);
        Route::post(uri: 'address/update', action: [AddressPreferencesController::class, 'updateAddress']);
    });


});

Route::get('countries', [CountryController::class, 'index']);
Route::get('countries/{country}/states', [CountryController::class, 'states']);

Route::prefix('v1')->group(function () {
    Route::prefix('news')->group(function () {
        Route::get('recommendations', [NewsRecommendationController::class, 'index']);
        Route::get('/', [NewsApiController::class, 'index']);
        Route::get('categories/{category}', [NewsApiController::class, 'category']);
        Route::get('tags/{tag}', [NewsApiController::class, 'tag']);
        Route::get('{newsItem}', [NewsApiController::class, 'show']);
    });
});
