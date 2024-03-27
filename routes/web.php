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

    Route::resource('users', UsersController::class);
    Route::post('/get-address-info', RapidApiController::class)->name('get.address.info');
    Route::resource('business',BusinessController::class);

    // Logs Route
    // Route::get('logs/all', [LogsController::class, 'getAllLogs'])->name('logs.all');
    // Route::match(['get', 'post'], 'logs/{userId}', [LogsController::class, 'getLogsByUser'])->name('user.logs');
    // Route::match(['get', 'post'], 'logs/{subjectId}/{subjectType}', [LogsController::class, 'getLogsBySubject'])->name('subject.logs');


    // Route::post('logs/add', [LogsController::class, 'store'])->name('logs.store');

});

Route::get('/setting', function () {
    return view('modules.business.setting');
});

Route::get('/business/members', function () {
    return view('modules.business.members');
})->name('business.members');

// Route::get('/business/list', function () {
//     return view('modules.business.index');
// })->name('business.list');

Route::get('/business/setting', function () {
    return view('modules.business.setting');
})->name('business.setting');

Route::get('/business/posts', function () {
    return view('modules.business.posts');
})->name('business.posts.list');

// Route::get('/create', function () {
//     return view('modules.business.create');
// })->name('business.create');

Route::get('/show', function () {
    return view('modules.business.show');
})->name('business.show');

Route::get('/profile', function () {
    return view('modules.profile.show');
})->name('profile');

Route::get(
    'locale/{lang}',
    function ($lang) {
        Session::put('lang', $lang);
        return back();
    }
)->name('change-locale');

require __DIR__ . '/auth.php';
