<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegistrationController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\API\BusinessController;
use App\Http\Controllers\API\BusinessTypesController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\FeedController;
use App\Http\Controllers\API\PostsController;

Route::post('/login', LoginController::class);
Route::post('/register', RegistrationController::class);
Route::post('/password/reset', ResetPasswordController::class);
Route::get('/countries/{country}/states', [CountryController::class, 'states']);
Route::get('business/types', [BusinessTypesController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('business/types/{id}', [BusinessTypesController::class, 'getById']);
    Route::get('posts/{id}', [PostsController::class, 'getById']);
    Route::get('business/{id}', [BusinessController::class, 'getById']);

    Route::get('posts', [PostsController::class, 'index']);

    Route::post('posts/{id}/like', [PostsController::class, 'likeUnlike']);
    Route::get('businesses', [BusinessController::class, 'getBusinesses']);
    Route::post('/comments/add', [PostsController::class, 'addComment']);

    Route::get('post/{id}/comments', [PostsController::class, 'getComments']);
    Route::group(['prefix' => 'business'], function () {
        Route::get('user/following', [BusinessController::class, 'following']);
        Route::get('posts', [BusinessController::class, 'posts']);
        Route::get('products', [BusinessController::class, 'products']);
        Route::get('notices', [BusinessController::class, 'notices']);
        Route::post('follow/{id}', [BusinessController::class, 'followUnfollow']);
    });
});
