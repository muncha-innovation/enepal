<?php

use App\Http\Controllers\APIS\LanguageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuth\LoginController;
use App\Http\Controllers\ApiAuth\RegistrationController;
use App\Http\Controllers\ApiAuth\ResetPasswordController;
use App\Http\Controllers\APIS\BusinessController;
use App\Http\Controllers\APIS\BusinessTypesController;
use App\Http\Controllers\APIS\CountryController;
use App\Http\Controllers\APIS\GalleryController;
use App\Http\Controllers\APIS\NotificationController;
use App\Http\Controllers\APIS\PostsController;
use App\Http\Controllers\APIS\AddressPreferencesController;
use App\Http\Controllers\APIS\ProductsController;
use App\Http\Controllers\APIS\UsersController;
use App\Http\Controllers\APIS\NewsApiController;
use App\Http\Controllers\APIS\NewsRecommendationController;
use App\Http\Controllers\APIS\SearchController;
use App\Http\Controllers\APIS\UserProfileController;
use App\Http\Controllers\APIS\UserPreferenceController;

Route::post('reset-password', ResetPasswordController::class);

Route::post('/login', LoginController::class);
Route::post('/register', RegistrationController::class);
Route::post('/password/reset', ResetPasswordController::class);
Route::get('/countries/{country}/states', [CountryController::class, 'states']);
Route::get('business/types', [BusinessTypesController::class, 'index']);

Route::get('post/{id}/comments', [PostsController::class, 'getComments']);
Route::get('posts/nearby', [PostsController::class, 'nearby']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [UsersController::class, 'user']);
    Route::post('/user/update', [UsersController::class, 'update']);
    Route::post('/user/image/update', [UsersController::class, 'updateImage']);
    Route::post('/user/password/update', [UsersController::class, 'updatePassword']);
    Route::get('business/types/{id}', [BusinessTypesController::class, 'getById']);
    Route::get('posts/{id}', [PostsController::class, 'getById']);
    Route::get('business/{id}', [BusinessController::class, 'getById']);

    Route::get('posts', [PostsController::class, 'index']);

    Route::post('posts/{id}/like', [PostsController::class, 'likeUnlike']);
    Route::get('businesses', [BusinessController::class, 'getBusinesses']);
    Route::get('businesses/me', [BusinessController::class, 'getMyBusinesses']);
    Route::post('business/add', [BusinessController::class, 'addBusiness']);
    Route::post('business/member/add', [BusinessController::class, 'addMember']);
    Route::post('/comments/add', [PostsController::class, 'addComment']);

    Route::post('/fcm/update', [UsersController::class, 'updateFcmToken']);
    Route::post('/news-preferences/toggle/{category}', [UsersController::class, 'toggleNewsPreference']);

    Route::get('products', [ProductsController::class, 'index']);
    Route::get('products/{id}', [ProductsController::class, 'getById']);

    Route::get('galleries', [GalleryController::class, 'index']);
    Route::get('galleries/{id}', [GalleryController::class, 'getById']);

    Route::get('notifications/user', [NotificationController::class, 'userNotifications']);
    Route::get('notifications/business', [NotificationController::class, 'businessNotifications']);
    Route::post('notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    Route::group(['prefix' => 'business'], function () {
        Route::get('user/following', [BusinessController::class, 'following']);
        Route::get('posts', [BusinessController::class, 'posts']);
        Route::get('products', [BusinessController::class, 'products']);
        Route::get('galleries', [BusinessController::class, 'galeries']);

        Route::post('follow/{id}', [BusinessController::class, 'followUnfollow']);
    });
    Route::group(['prefix' => 'address/preferences'], function () {
        Route::get('fetch', [AddressPreferencesController::class, 'fetch']);
        Route::post('update', [AddressPreferencesController::class, 'update']);
        Route::post(uri: 'address/update', action: [AddressPreferencesController::class, 'updateAddress']);
    });

    // User profile - Education routes
    Route::get('/profile/education', [UserProfileController::class, 'getEducations']);
    Route::get('/profile/education/{id}', [UserProfileController::class, 'getEducationItem']);
    Route::post('/profile/education', [UserProfileController::class, 'updateEducation']);
    Route::delete('/profile/education/{id}', [UserProfileController::class, 'deleteEducation']);
    
    // User profile - Work experience routes
    Route::get('/profile/work-experience', [UserProfileController::class, 'getWorkExperiences']);
    Route::get('/profile/work-experience/{id}', [UserProfileController::class, 'getWorkExperienceItem']);
    Route::post('/profile/work-experience', [UserProfileController::class, 'updateWorkExperience']);
    Route::delete('/profile/work-experience/{id}', [UserProfileController::class, 'deleteWorkExperience']);

    // User preferences API endpoints
    Route::prefix('preferences')->group(function () {
        Route::get('/', [UserPreferenceController::class, 'show']);
        Route::post('/', [UserPreferenceController::class, 'update']);
        Route::get('/news-categories', [UserPreferenceController::class, 'getNewsCategories']);
        Route::post('/news', [UserPreferenceController::class, 'updateNewsPreferences']);
        Route::post('/news/bulk', [UserPreferenceController::class, 'bulkUpdateNewsPreferences']);
    });


    Route::post('/profile/preferences', [UserProfileController::class, 'updatePreferences']);
});

// User Segment Management Routes
// Route::prefix('business/{business}/segments')->group(function () {
//     Route::get('preview/{segmentId}', [SegmentController::class, 'previewCount']);
//     Route::post('/', [SegmentController::class, 'store']);
//     Route::put('{segment}', [SegmentController::class, 'update']);
//     Route::delete('{segment}', [SegmentController::class, 'destroy']);
// });

Route::get('countries', [CountryController::class, 'index']);
Route::get('countries/{country}/states', [CountryController::class, 'states']);

Route::get('languages', [LanguageController::class, 'index']);

Route::prefix('v1')->group(function () {
    Route::prefix('news')->group(function () {
        Route::get('primary', [NewsApiController::class, 'primary']);
        Route::get('secondary', [NewsApiController::class, 'secondary']);
        Route::get('/', [NewsApiController::class, 'index']);
        Route::get('categories/{category}', [NewsApiController::class, 'category']);
        Route::get('tags/{tag}', [NewsApiController::class, 'tag']);
        Route::get('{newsItem}', [NewsApiController::class, 'show']);
    });

   
});
 // Chat API Routes
 Route::middleware(['auth:sanctum'])->group(function () {
    // Conversations
    Route::apiResource('conversations', \App\Http\Controllers\Api\ConversationController::class);
    
    // Threads
    Route::get('conversations/{conversation}/threads', [\App\Http\Controllers\Api\ThreadController::class, 'index']);
    Route::post('conversations/{conversation}/threads', [\App\Http\Controllers\Api\ThreadController::class, 'store']);
    Route::get('threads/{thread}', [\App\Http\Controllers\Api\ThreadController::class, 'show']);
    Route::put('threads/{thread}', [\App\Http\Controllers\Api\ThreadController::class, 'update']);
    Route::delete('threads/{thread}', [\App\Http\Controllers\Api\ThreadController::class, 'destroy']);
    Route::post('threads/{thread}/mark-read', [\App\Http\Controllers\Api\ThreadController::class, 'markAsRead']);
    
    // Messages
    Route::get('threads/{thread}/messages', [\App\Http\Controllers\Api\MessageController::class, 'index']);
    Route::post('threads/{thread}/messages', [\App\Http\Controllers\Api\MessageController::class, 'store']);
    Route::get('messages/{message}', [\App\Http\Controllers\Api\MessageController::class, 'show']);
    Route::post('messages/{message}/mark-read', [\App\Http\Controllers\Api\MessageController::class, 'markAsRead']);
    Route::delete('messages/{message}', [\App\Http\Controllers\Api\MessageController::class, 'destroy']);
});
Route::prefix('search')->group(function () {
    Route::get('/', [SearchController::class, 'search']);
    Route::get('/posts', [SearchController::class, 'searchPosts']);
    Route::get('/news', [SearchController::class, 'searchNews']); 
    Route::get('/businesses', [SearchController::class, 'searchBusinesses']);
});
