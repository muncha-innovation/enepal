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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapidApiController;

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
        Route::get('members/{business}', [BusinessController::class, 'members'])->name('members');
        Route::match(['get', 'post'], 'add-member/{business}', [BusinessController::class, 'addMember'])->name('member.add');
        Route::get('setting/{business}', [BusinessController::class, 'setting'])->name('setting');
        Route::get('posts/{business}', [BusinessController::class, 'posts'])->name('posts.list');
        Route::get('post/create/{business}', [BusinessController::class, 'createPost'])->name('post.create');
    });
    Route::resource('business', BusinessController::class);


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

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
