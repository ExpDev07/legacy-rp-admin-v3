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

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SteamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PlayerBanController;
use App\Http\Controllers\PlayerCharacterController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerWarningController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;
use kanalumaddela\LaravelSteamLogin\Facades\SteamLogin;

// Authentication methods.
Route::group([ 'prefix' => 'auth' ], function ()
{
    SteamLogin::routes([ 'controller' => SteamController::class ]);
});

// Logging in and out.
Route::group([ 'namespace' => 'Auth' ], function()
{
    Route::name('login')->get('/login', [ LoginController::class, 'render' ]);
    Route::name('logout')->post('/logout', [ LogoutController::class, 'logout' ]);
});

// Routes requiring being logged in as a staff member.
Route::group([ 'middleware' => [ 'auth', 'staff' ] ], function ()
{
    // Home.
    Route::get('/', [ HomeController::class, 'render' ]);

    // Players.
    Route::resource('players', PlayerController::class);
    Route::resource('players.characters', PlayerCharacterController::class);
    Route::resource('players.bans', PlayerBanController::class);
    Route::resource('players.warnings', PlayerWarningController::class);

    // Logs.
    Route::resource('logs', LogController::class);

    // Servers.
    Route::resource('servers', ServerController::class);

});

// Used for testing purposes.
Route::get('/test', function () {});
