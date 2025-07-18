<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CommunicationsController;
use App\Http\Controllers\BusinessNotificationController;
use App\Http\Middleware\StatusMiddleware;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapidApiController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SegmentController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth', StatusMiddleware::class, 'role:user|super-admin', 'force.password.update','user.inactive.check']], function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Route::resource('users', UsersController::class);
    Route::post('/get-address-info', RapidApiController::class)->name('get.address.info');
    
    Route::post('image/upload', [BusinessController::class, 'uploadImage'])->name('image.upload');
    Route::group(['prefix' => 'business', 'as' => 'business.'], function () {
        Route::get('setting/{business}', [BusinessController::class, 'setting'])->name('setting');
        Route::post('verify/{business}', [BusinessController::class, 'verify'])->name('verify');
        Route::get('{business}/owner-profile', [BusinessController::class, 'ownerProfile'])->name('owner-profile');

        // Section-specific save routes
        Route::post('save-general', [BusinessController::class, 'saveGeneral'])->name('saveGeneral.create');
        Route::put('{business}/save-general', [BusinessController::class, 'saveGeneral'])->name('saveGeneral.update');
        Route::put('{business}/save-details', [BusinessController::class, 'saveDetails'])->name('saveDetails');
        Route::put('{business}/save-address', [BusinessController::class, 'saveAddress'])->name('saveAddress');
        Route::put('{business}/save-social-media', [BusinessController::class, 'saveSocialMedia'])->name('saveSocialMedia');
        Route::put('{business}/save-manpower-consultancy', [BusinessController::class, 'saveManpowerConsultancy'])->name('saveManpowerConsultancy');
        Route::get('language-row/{index}', [BusinessController::class, 'getLanguageRow'])->name('getLanguageRow');
        Route::get('destination-row/{index}', [BusinessController::class, 'getDestinationRow'])->name('getDestinationRow');
        
        // Deprecated - keeping for backward compatibility
        Route::put('{business}/save-contact', [BusinessController::class, 'saveContact'])->name('saveContact');
    });

    Route::group(['prefix' => 'members', 'as' => 'members.'], function() {
        Route::get('/{business}', [MembersController::class, 'index'])->name('index');
        Route::get('create/{business}', [MembersController::class, 'create'])->name('create');
        Route::post('store/{business}', [MembersController::class, 'store'])->name('store');
        Route::get('edit/{business}/{user}', [MembersController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{user}', [MembersController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{user}', [MembersController::class, 'destroy'])->name('destroy');
        
        // Segment management routes
        Route::get('{business}/segments', [SegmentController::class, 'index'])->name('segments.index');
        Route::post('{business}/segments', [SegmentController::class, 'store'])->name('segments.store');
    Route::put('{business}/segments/{segment}', [SegmentController::class, 'update'])->name('segments.update');
        Route::delete('{business}/segments/{segment}', [SegmentController::class, 'destroy'])->name('segments.destroy');
        Route::get('{business}/segments/{segment}/preview', [SegmentController::class, 'preview'])->name('segments.preview');
        Route::post('{business}/segments/{segment}/users', [SegmentController::class, 'addUsers'])->name('segments.users.add');
        Route::delete('{business}/segments/{segment}/users', [SegmentController::class, 'removeUsers'])->name('segments.users.remove');
        
        // User-segment management route
        Route::post('{business}/user-segments/{user}', [SegmentController::class, 'updateUserSegments'])->name('user.segments.update');
    });

    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function() {
        Route::get('/{business}', [PostController::class, 'index'])->name('index');
        Route::get('create/{business}', [PostController::class, 'create'])->name('create');
        Route::post('create/{business}', [PostController::class, 'store'])->name('store');
        Route::get('show/{business}/{post}', [PostController::class, 'show'])->name('show');
        Route::get('edit/{business}/{post}', [PostController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{post}', [PostController::class, 'destroy'])->name('destroy');
        
        // Comment routes
        Route::post('{business}/{post}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
        Route::get('{business}/{post}/comments', [App\Http\Controllers\CommentController::class, 'getPostComments'])->name('comments.get');
    });
    Route::resource('business', BusinessController::class);
    
    // Business Communication Routes
    Route::prefix('business/{business}/communications')->group(function () {
        Route::get('/', [CommunicationsController::class, 'getConversations'])->name('business.communications.index');
        Route::post('/chat/create', [CommunicationsController::class, 'createChat'])->name('business.communications.createChat');
        Route::post('/notification/send', [BusinessNotificationController::class, 'sendNotification'])->name('business.communications.sendNotification');
        Route::get('/notification/{notification}/stats', [CommunicationsController::class,'stats'])->name('business.notification.stats');
        Route::get('conversation/{conversation}', [CommunicationsController::class, 'getMessages'])->name('business.communications.messages');
        Route::post('/conversation/{conversation}/send', [CommunicationsController::class, 'sendMessage'])->name('business.communications.send');
        Route::post('/conversation/{conversation}/thread', [CommunicationsController::class, 'createThread'])->name('business.communications.createThread');
        Route::delete('/conversation/{conversation}/thread/{thread}', [CommunicationsController::class, 'deleteThread'])->name('business.communications.deleteThread');
        Route::get('/notifications/{notification}/read', [BusinessNotificationController::class, 'markNotificationAsRead'])->name('business.communications.markRead');
        Route::post('/notifications/read-all', [BusinessNotificationController::class, 'markAllNotificationsAsRead'])->name('business.communications.markAllRead');
    });
    Route::get('/search-users', [CommunicationsController::class, 'searchUsers'])->name('search-users');
    
    Route::post('{business}/restore', [BusinessController::class, 'restore'])->name('business.restore');
    Route::post('business/{business}/featured', [BusinessController::class, 'featured'])->name('business.featured');

    Route::group(['prefix' => 'products', 'as' => 'products.'], function() {
        Route::get('/{business}', [ProductController::class, 'index'])->name('index');
        Route::get('create/{business}', [ProductController::class, 'create'])->name('create');
        Route::post('create/{business}', [ProductController::class, 'store'])->name('store');
        Route::get('show/{business}/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('edit/{business}/{product}', [ProductController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'gallery', 'as' => 'gallery.'], function() {
        Route::get('/{business}', [GalleryController::class, 'index'])->name('index');
        Route::get('create/{business}', [GalleryController::class, 'create'])->name('create');
        Route::post('create/{business}', [GalleryController::class, 'store'])->name('store');
        Route::get('show/{business}/{gallery}', [GalleryController::class, 'show'])->name('show');
        Route::get('edit/{business}/{gallery}', [GalleryController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{gallery}', [GalleryController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{gallery}', [GalleryController::class, 'destroy'])->name('destroy');
    });

    // Comment management routes
    Route::group(['prefix' => 'comments', 'as' => 'comments.'], function() {
        Route::get('/{business}', [App\Http\Controllers\CommentController::class, 'index'])->name('index');
        Route::post('/{business}/{comment}/approve', [App\Http\Controllers\CommentController::class, 'approve'])->name('approve');
        Route::delete('/{business}/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])->name('destroy');
    });

    
    Route::resource('galleryImage', GalleryImageController::class);

    // User preferences routes
    Route::get('/profile/preferences', [ProfileController::class, 'preferences'])->name('profile.preferences');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');

});

// Profile routes - separate middleware group to avoid redirect loop with user.inactive.check
Route::group(['middleware' => ['auth', StatusMiddleware::class, 'role:user|super-admin', 'force.password.update']], function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage'])->name('profile.upload-image');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/profile/work-experience', [ProfileController::class, 'getWorkExperience'])->name('profile.workExperience');
    Route::get('/profile/work-experience/{id}', [ProfileController::class, 'getWorkExperienceItem'])->name('profile.workExperience.show');
    Route::post('/profile/work-experience', [ProfileController::class, 'updateWorkExperience'])->name('profile.workExperience.update');
    Route::delete('/profile/work-experience/{id}', [ProfileController::class, 'deleteWorkExperience'])->name('profile.workExperience.delete');

    Route::get('/profile/education', [ProfileController::class, 'getEducation'])->name('profile.education');
    Route::get('/profile/education/{id}', [ProfileController::class, 'getEducationItem'])->name('profile.education.show');
    Route::post('/profile/education', [ProfileController::class, 'updateEducation'])->name('profile.education.update');
    Route::delete('/profile/education/{id}', [ProfileController::class, 'deleteEducation'])->name('profile.education.delete');
});

// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', function () {
        return view('chat.index');
    })->name('chat.index');
});

Route::get('password/update', [ProfileController::class, 'passwordUpdate'])->name('profile.password.update');

Route::get('/home', function () {
    return view('modules.frontend.index');
})->name('home');

Route::get('/single', function () {
    return view('modules.frontend.resturant');
})->name('single');

Route::get('/menu', function () {
    return view('modules.frontend.menu');
})->name('menu');

Route::get('/posts', function () {
    return view('modules.frontend.posts');
})->name('posts');

Route::get('/chat', function () {
    return view('modules.frontend.chat');
})->name('chat');

Route::get('/location', function () {
    return view('modules.frontend.location');
})->name('location');

Route::get(
    'locale/{lang}',
    function ($lang) {
        Session::put('lang', $lang);
        return back();
    }
)->name('change-locale');

Route::get('send/mail', function() {
    Mail::to('cooloozewall@gmail.com')->send(new \App\Mail\TestMail());
});

// Public News Routes
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'publicIndex'])->name('public.index');
    Route::get('/{newsItem}', [NewsController::class, 'publicShow'])->name('public.show');
    Route::get('/category/{category}', [NewsController::class, 'publicCategory'])->name('public.category');
});

// Public Post Routes  
Route::prefix('post')->name('frontend.post.')->group(function () {
    Route::get('/{post}', [PostController::class, 'frontendShow'])->name('show');
});

Route::get('/telescope-status', fn() => [
    'enabled' => config('telescope.enabled'),
    'app_env' => app()->environment(),
    'telescope_service_provider_registered' => class_exists(\Laravel\Telescope\TelescopeServiceProvider::class),
]);

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';

