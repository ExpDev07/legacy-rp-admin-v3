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

// Logging in.
Route::get('/login', 'Auth\LoginController')->name('login');

// Grouping of all the authentication routes together under /auth.
Route::prefix('auth')->group(function () {

    // Allow users to login using their steam account.
    // - {host}:{port}/auth/login/steam
    // - {host}:{port}/auth/auth/steam
    SteamLogin::routes(['controller' => SteamController::class]);

});



// Group routes that require authentication together.
Route::middleware('auth')->group(function () {



});

