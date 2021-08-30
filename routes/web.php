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

use App\Http\Controllers\AdvancedSearchController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SteamController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PanelLogController;
use App\Http\Controllers\PlayerBanController;
use App\Http\Controllers\PlayerCharacterController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerRouteController;
use App\Http\Controllers\PlayerWarningController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SuspiciousController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TwitterController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use kanalumaddela\LaravelSteamLogin\Facades\SteamLogin;

// Authentication methods.
Route::group(['prefix' => 'auth'], function () {
    SteamLogin::routes(['controller' => SteamController::class]);
});

// Logging in and out.
Route::group(['namespace' => 'Auth'], function () {
    Route::name('login')->get('/login', [LoginController::class, 'render']);
    Route::name('logout')->post('/logout', [LogoutController::class, 'logout']);
});

// Routes requiring being logged in as a staff member.
Route::group(['middleware' => ['log', 'staff']], function () {
    // Home.
    Route::get('/', [HomeController::class, 'render']);
    Route::get('/changelog', [ChangelogController::class, 'render']);

    // Players.
    Route::resource('players', PlayerController::class);
    Route::resource('players.characters', PlayerCharacterController::class);
    Route::resource('players.bans', PlayerBanController::class);
    Route::resource('players.warnings', PlayerWarningController::class);
    Route::post('/players/{player}/kick', [PlayerRouteController::class, 'kick']);
    Route::post('/players/{player}/staffPM', [PlayerRouteController::class, 'staffPM']);
    Route::post('/players/{player}/unloadCharacter', [PlayerRouteController::class, 'unloadCharacter']);
    Route::get('/players/{player}/linked', [PlayerRouteController::class, 'linkedAccounts']);
    Route::delete('/players/{player}/removeIdentifier/{identifier}', [PlayerRouteController::class, 'removeIdentifier']);

    // Inventories.
    Route::get('/inventories/character/{character}', [InventoryController::class, 'character']);
    Route::get('/inventories/vehicle/{vehicle}', [InventoryController::class, 'vehicle']);
    Route::get('/inventories/property/{property}', [InventoryController::class, 'property']);
    Route::get('/inventory/{inventory}', [InventoryController::class, 'show']);
    Route::post('/inventory/{inventory}/createSnapshot', [InventoryController::class, 'createSnapshot']);
    Route::get('/inventory/snapshot/{snapshot}', [InventoryController::class, 'showSnapshot']);
    Route::get('/inventory_find/{type}/{id}', [InventoryController::class, 'find']);
    Route::delete('/inventory/{inventory}/clear/{slot}', [InventoryController::class, 'clear']);
    Route::get('/search_inventory', [InventoryController::class, 'search']);

    // Advanced search.
    Route::get('/advanced', [AdvancedSearchController::class, 'index']);

    // Suspicious.
    Route::get('/suspicious', [SuspiciousController::class, 'index']);

    // Twitter.
    Route::resource('twitter', TwitterController::class);
    Route::post('tweets/delete/{post}', [TwitterController::class, 'deleteTweet']);

    // Logs.
    Route::resource('logs', LogController::class);

    // Panel Logs.
    Route::resource('panel_logs', PanelLogController::class);

    // Characters.
    Route::resource('characters', PlayerCharacterController::class);
    Route::post('vehicles/delete/{vehicle}', [PlayerCharacterController::class, 'deleteVehicle']);
    Route::post('vehicles/edit/{vehicle}', [PlayerCharacterController::class, 'editVehicle']);
    Route::post('/players/{player}/characters/{character}/removeTattoos', [PlayerCharacterController::class, 'removeTattoos']);
    Route::post('/players/{player}/characters/{character}/resetSpawn', [PlayerCharacterController::class, 'resetSpawn']);
    Route::put('/players/{player}/characters/{character}/editBalance', [PlayerCharacterController::class, 'editBalance']);

    // Servers.
    Route::resource('servers', ServerController::class);

    // Map.
    Route::get('/map', [MapController::class, 'index']);
    Route::get('/map/data', [MapController::class, 'data']);

    // Statistics.
    Route::get('/statistics', [StatisticsController::class, 'render']);

    // Testing.
    Route::post('test/{token}/set_tattoos/{character}/{zone}', [TestController::class, 'setTattoos']);
});

Route::group(['middleware' => ['staff'], 'prefix' => 'api'], function () {
    // Player count api
    Route::get('players', [HomeController::class, 'playerCountApi']);

    // Character info api
    Route::post('characters', [PlayerCharacterController::class, 'getCharacters']);
});

// Used to get logs.
Route::get('/op-logs/{type}/{api_key}/{date?}', function (string $type, string $api_key, string $date = '') {
    if (!$date) {
        $date = date('Y-m-d');
    } else {
        $date = preg_replace('/[^\d-]/m', '', $date);
    }

    $file = '';
    switch ($type) {
        case 'default':
            $file = storage_path('logs/op-fw-' . $date . '.log');
            break;
        case 'access':
            $file = storage_path('logs/op-fw-access-' . $date . '.log');
            break;
        case 'error':
            $file = storage_path('logs/error-' . $date . '.log');
            break;
    }

    return doOPLogFileDownload($file, $api_key);
});

function doOPLogFileDownload(string $path, string $api_key)
{
    if (!$path) {
        return (new Response('Invalid file', 400))
            ->header('Content-Type', 'text/plain');
    }

    if (env('DEV_API_KEY', '') === $api_key && !empty($api_key)) {
        if (!file_exists($path)) {
            return (new Response('Empty file', 200))
                ->header('Content-Type', 'text/plain');
        }
        return response()->download($path, basename($path));
    }

    return (new Response('Unauthorized', 403))
        ->header('Content-Type', 'text/plain');
}

// Used for testing purposes.
Route::get('/test', function () {
    return (new Response('Hash: ' . md5($_SERVER['REMOTE_ADDR']), 200))
        ->header('Content-Type', 'text/plain');
});
