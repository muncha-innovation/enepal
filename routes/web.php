<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\ChecklistController;
use App\Http\Middleware\StatusMiddleware;
use App\Models\Process;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapidApiController;
use App\Http\Controllers\ProductController;

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


Route::group(['middleware' => ['auth', StatusMiddleware::class, 'role:user|super-admin', 'force.password.update']], function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Route::resource('users', UsersController::class);
    Route::post('/get-address-info', RapidApiController::class)->name('get.address.info');
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
    });

    Route::group(['prefix' => 'posts', 'as' => 'posts.'], function() {
        Route::get('/{business}', [PostController::class, 'index'])->name('index');
        Route::get('create/{business}', [PostController::class, 'create'])->name('create');
        Route::post('create/{business}', [PostController::class, 'store'])->name('store');
        Route::get('show/{business}/{post}', [PostController::class, 'show'])->name('show');
        Route::get('edit/{business}/{post}', [PostController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{post}', [PostController::class, 'destroy'])->name('destroy');
        Route::post('{business}/image/upload', [PostController::class, 'uploadImage'])->name('image.upload');
    });
    Route::resource('business', BusinessController::class);
    Route::group(['prefix' => 'products', 'as' => 'products.'], function() {
        Route::get('/{business}', [ProductController::class, 'index'])->name('index');
        Route::get('create/{business}', [ProductController::class, 'create'])->name('create');
        Route::post('create/{business}', [ProductController::class, 'store'])->name('store');
        Route::get('show/{business}/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('edit/{business}/{product}', [ProductController::class, 'edit'])->name('edit');
        Route::put('update/{business}/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('delete/{business}/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('{business}/image/upload', [ProductController::class, 'uploadImage'])->name('image.upload');
    });
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

Route::get('/posts', function () {
    return view('modules.frontend.posts');
})->name('posts');

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

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
