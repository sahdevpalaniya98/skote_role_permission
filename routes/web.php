<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\HomeController;

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

Route::group(['prefix' => 'commands'], function () {
    Route::get('/optimize-clear', function() {
        Artisan::call('optimize:clear');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/migrate', function() {
        Artisan::call('migrate');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/clear-compiled', function() {
        Artisan::call('clear-compiled');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/activitylog-clean', function() {
        Artisan::call('activitylog:clean');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/migrate-fresh', function() {
        Artisan::call('migrate:fresh');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/seed', function() {
        Artisan::call('db:seed');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/db-wipe', function() {
        Artisan::call('db:wipe');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/migrate-rollback', function() {
        Artisan::call('migrate:rollback');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });

    Route::get('/permission-cache-reset', function() {
        Artisan::call('permission:cache-reset');
        echo '<pre>'.str_replace('\n', "\n", Artisan::output()).'</pre>';
    });
});


// Route::get('/test', function () {
//     return view('layouts.guest');
// });

Route::get('/', [LoginController::class, 'showLoginForm']);

Auth::routes(['verify' => true, 'logout' => false]);

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::group(['as' => 'admin.'], function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
        Route::post('/profile/update', [ProfileController::class, 'profileUpdate'])->name('profile.submit');
        Route::post('/password/update', [ProfileController::class, 'passwordUpdate'])->name('password.submit');
     
        Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/datatable', [UserController::class, 'datatable'])->name('data');
            Route::get('/add', [UserController::class, 'create'])->name('add');
            Route::post('/exists', [UserController::class, 'exists'])->name('exists');
            Route::post('/store', [UserController::class, 'store'])->name('store');
            Route::get('/view/{id}', [UserController::class, 'show'])->name('view');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
            Route::post('/destroy', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/status/change', [UserController::class, 'statusChange'])->name('status.change');
            Route::get('/history/{id}', [UserController::class, 'history'])->name('history');
            Route::post('/history-datatable/{id}', [UserController::class, 'history_datatable'])->name('history.data');
        });

        Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/datatable', [RoleController::class, 'datatable'])->name('data');
            Route::get('/add', [RoleController::class, 'create'])->name('add');
            Route::post('/exists', [RoleController::class, 'exists'])->name('exists');
            Route::post('/store', [RoleController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
            Route::post('/destroy', [RoleController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'permission', 'as' => 'permission.'], function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/datatable', [PermissionController::class, 'datatable'])->name('data');
            Route::get('/add', [PermissionController::class, 'create'])->name('add');
            Route::post('/exists', [PermissionController::class, 'exists'])->name('exists');
            Route::post('/store', [PermissionController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('edit');
            Route::post('/destroy', [PermissionController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'activity', 'as' => 'activity.'], function () {
            Route::get('/', [ActivityController::class, 'index'])->name('index');
            Route::post('/datatable', [ActivityController::class, 'datatable'])->name('data');
        });
    });
});
