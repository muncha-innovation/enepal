<?php

use App\Events\NotificationCreated;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ChecklistController;
use App\Http\Middleware\StatusMiddleware;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GalleryImageController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapidApiController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

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
    Route::get('/businesses/facilities', [BusinessController::class, 'getFacilities']);
    Route::group(['prefix' => 'business', 'as' => 'business.'], function () {
        Route::get('setting/{business}', [BusinessController::class, 'setting'])->name('setting');
        Route::post('verify/{business}', [BusinessController::class, 'verify'])->name('verify');

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
    Route::group(['prefix' => 'notices', 'as' => 'notices.'], function() {
        Route::get('/{business}', [NoticeController::class, 'index'])->name('index');
        Route::get('create/{business}', [NoticeController::class, 'create'])->name('create');
        Route::post('create/{business}', [NoticeController::class, 'store'])->name('store');
        Route::get('show/{business}/{notice}', [NoticeController::class, 'show'])->name('show');
        Route::get('edit/{business}/{notice}', [NoticeController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{notice}', [NoticeController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{notice}', [NoticeController::class, 'destroy'])->name('destroy');
        Route::get('verify/{business}/{notice}', [NoticeController::class, 'verify'])->name('verify');
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
    // Logs Route
    // Route::get('logs/all', [LogsController::class, 'getAllLogs'])->name('logs.all');
    // Route::match(['get', 'post'], 'logs/{userId}', [LogsController::class, 'getLogsByUser'])->name('user.logs');
    // Route::match(['get', 'post'], 'logs/{subjectId}/{subjectType}', [LogsController::class, 'getLogsBySubject'])->name('subject.logs');


    // Route::post('logs/add', [LogsController::class, 'store'])->name('logs.store');

});

Route::get('password/update', [ProfileController::class, 'passwordUpdate'])->name('password.update');

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

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';

