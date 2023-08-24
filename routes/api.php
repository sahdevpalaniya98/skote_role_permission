<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ContestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Auth::routes(['verify' => true]);
Route::group([
    'namespace' => 'App\Http\Controllers\Api',
], function () {
    Route::post('/login', 'AuthController@DoLogin');
	Route::post('register', 'AuthController@DoRegister');
    Route::post('ForgotPassword', 'AuthController@ForgotPassword');
    Route::group([
        'middleware' => ['auth:api','APIToken']
    ], function() {
        Route::get('/logout', 'AuthController@logout');
        Route::post('/contest', 'ContestController@index');
        Route::post('/contest_questions', 'ContestController@contest_questions');
        //login route
    });
});

