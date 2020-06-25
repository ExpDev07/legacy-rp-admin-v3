<?php

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

use App\Http\Controllers\Auth\SteamController;
use Illuminate\Support\Facades\Route;
use kanalumaddela\LaravelSteamLogin\Facades\SteamLogin;

// Authentication methods.
Route::group([ 'prefix' => 'auth' ], function () {
    SteamLogin::routes([ 'controller' => SteamController::class ]);
});

// Logging in and out.
Route::group([ 'namespace' => 'Auth' ], function() {
    Route::name('login')->get('/login', 'LoginController@render');
    Route::name('logout')->post('/logout', 'LogoutController@logout');
});

// Routes requiring being logged in.
Route::group([ 'middleware' => [ 'auth', 'staff' ] ], function () {
    Route::get('/', 'HomeController@render');

    // Players.
    Route::resource('players', 'PlayerController');
    Route::resource('players.characters', 'PlayerCharacterController');
    Route::resource('players.bans', 'PlayerBanController');
    Route::resource('players.warnings', 'PlayerWarningController');

    // Logs.
    Route::resource('logs', 'LogController');
});


