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


Route::middleware(['auth', StatusMiddleware::class])->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Route::resource('users', UsersController::class);
    Route::post('/get-address-info', RapidApiController::class)->name('get.address.info');
    Route::group(['prefix'=>'business', 'as' => 'business.'],function() {
        Route::get('members/{business}', [BusinessController::class, 'members'])->name('members');
        Route::get('add-member/{business}', [BusinessController::class, 'addMember'])->name('member.add');
        Route::get('setting/{business}', [BusinessController::class, 'setting'])->name('setting');
        Route::get('posts/{business}', [BusinessController::class, 'posts'])->name('posts.list');
    });
    Route::resource('business',BusinessController::class);
   

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Logs Route
    // Route::get('logs/all', [LogsController::class, 'getAllLogs'])->name('logs.all');
    // Route::match(['get', 'post'], 'logs/{userId}', [LogsController::class, 'getLogsByUser'])->name('user.logs');
    // Route::match(['get', 'post'], 'logs/{subjectId}/{subjectType}', [LogsController::class, 'getLogsBySubject'])->name('subject.logs');


    // Route::post('logs/add', [LogsController::class, 'store'])->name('logs.store');

});

Route::get('/home', function () {
    return view('modules.frontend.index');
});


Route::get(
    'locale/{lang}',
    function ($lang) {
        Session::put('lang', $lang);
        return back();
    }
)->name('change-locale');

require __DIR__ . '/auth.php';
