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
use Illuminate\Support\Facades\Broadcast;

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
Route::get('/pusher-test', function () {
    try {
        // This will send a manual broadcast to Pusher
        broadcast(new \Illuminate\Broadcasting\BroadcastEvent(
            'test-channel',
            'test.event',
            ['message' => 'Hello from Production!']
        ));

        return 'Test event broadcasted to Pusher.';
    } catch (\Exception $e) {
        Log::error('Pusher Test Error: '.$e->getMessage());
        return 'Error broadcasting: ' . $e->getMessage();
    }
});

Route::group(['middleware' => ['auth', StatusMiddleware::class, 'role:user|super-admin', 'force.password.update','user.inactive.check']], function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Route::resource('users', UsersController::class);
    Route::post('/get-address-info', RapidApiController::class)->name('get.address.info');
    
    Route::post('image/upload', [BusinessController::class, 'uploadImage'])->name('image.upload');
    Route::group(['prefix' => 'business', 'as' => 'business.'], function () {
        Route::get('setting/{business}', [BusinessController::class, 'setting'])->name('setting');
        Route::post('verify/{business}', [BusinessController::class, 'verify'])->name('verify');

        // Section-specific save routes
        Route::post('save-general', [BusinessController::class, 'saveGeneral'])->name('saveGeneral.create');
        Route::put('{business}/save-general', [BusinessController::class, 'saveGeneral'])->name('saveGeneral.update');
        Route::put('{business}/save-details', [BusinessController::class, 'saveDetails'])->name('saveDetails');
        Route::put('{business}/save-address', [BusinessController::class, 'saveAddress'])->name('saveAddress');
        Route::put('{business}/save-contact', [BusinessController::class, 'saveContact'])->name('saveContact');
    });

    Route::group(['prefix' => 'members', 'as' => 'members.'], function() {
        Route::get('/{business}', [MembersController::class, 'index'])->name('index');
        Route::get('create/{business}', [MembersController::class, 'create'])->name('create');
        Route::post('store/{business}', [MembersController::class, 'store'])->name('store');
        Route::get('edit/{business}/{user}', [MembersController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{user}', [MembersController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{user}', [MembersController::class, 'destroy'])->name('destroy');
    });

    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function() {
        Route::get('/{business}', [PostController::class, 'index'])->name('index');
        Route::get('create/{business}', [PostController::class, 'create'])->name('create');
        Route::post('create/{business}', [PostController::class, 'store'])->name('store');
        Route::get('show/{business}/{post}', [PostController::class, 'show'])->name('show');
        Route::get('edit/{business}/{post}', [PostController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{post}', [PostController::class, 'destroy'])->name('destroy');
    });
    Route::resource('business', BusinessController::class);
    
    // Business Communication Routes
    Route::prefix('business/{business}/communications')->group(function () {
        Route::get('/', [CommunicationsController::class, 'getConversations'])->name('business.communications.index');
        Route::get('/segments', [CommunicationsController::class, 'manageSegments'])->name('business.communications.segments.index');
        Route::post('/chat/create', [CommunicationsController::class, 'createChat'])->name('business.communications.createChat');
        Route::post('/notification/send', [BusinessNotificationController::class, 'sendNotification'])->name('business.communications.sendNotification');
        Route::get('conversation/{conversation}', [CommunicationsController::class, 'getMessages'])->name('business.communications.messages');
        Route::post('/conversation/{conversation}/send', [CommunicationsController::class, 'sendMessage'])->name('business.communications.send');
        Route::post('/conversation/{conversation}/thread', [CommunicationsController::class, 'createThread'])->name('business.communications.createThread');
        Route::delete('/conversation/{conversation}/thread/{thread}', [CommunicationsController::class, 'deleteThread'])->name('business.communications.deleteThread');
        Route::post('/notifications/{notification}/read', [BusinessNotificationController::class, 'markNotificationAsRead'])->name('business.communications.markRead');
        Route::post('/notifications/read-all', [BusinessNotificationController::class, 'markAllNotificationsAsRead'])->name('business.communications.markAllRead');
        Route::get('/search-users', [CommunicationsController::class, 'searchUsers'])->name('business.communications.search-users');
    });
    
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

    
    Route::resource('galleryImage', GalleryImageController::class);
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    // Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    // Logs Route
    // Route::get('logs/all', [LogsController::class, 'getAllLogs'])->name('logs.all');
    // Route::match(['get', 'post'], 'logs/{userId}', [LogsController::class, 'getLogsByUser'])->name('user.logs');
    // Route::match(['get', 'post'], 'logs/{subjectId}/{subjectType}', [LogsController::class, 'getLogsBySubject'])->name('subject.logs');


    // Route::post('logs/add', [LogsController::class, 'store'])->name('logs.store');

    // User preferences routes
    Route::get('/profile/preferences', [ProfileController::class, 'preferences'])->name('profile.preferences');
    Route::post('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');

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



require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';

