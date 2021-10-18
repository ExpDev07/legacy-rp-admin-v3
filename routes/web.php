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

use App\Helpers\SessionHelper;
use App\Http\Controllers\AdvancedSearchController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SteamController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\CronjobController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\OverwatchController;
use App\Http\Controllers\PanelLogController;
use App\Http\Controllers\PlayerBanController;
use App\Http\Controllers\PlayerCharacterController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerRouteController;
use App\Http\Controllers\PlayerWarningController;
use App\Http\Controllers\SerialsController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SuspiciousController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TwitterController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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

// Shortened redirects.
Route::group([], function () {
    Route::get('/p/{steam}', function (string $steam) {
        $steam = preg_replace('/[^\w]+/mi', '', $steam);
        $steam = base_convert($steam, 36, 16);

        if (!$steam) {
            abort(400);
        }

        if (!Str::startsWith($steam, 'steam:')) {
            $steam = 'steam:' . $steam;
        }

        return redirect('/players/' . $steam);
    });

    Route::get('/s/{session}', function (string $session, Request $request) {
        $session = preg_replace('/[^\w]+/mi', '', $session);
        if (!$session) {
            abort(400);
        }

        $back = $request->query('back');
        if (
            !Str::startsWith($back, "https://" . CLUSTER . '.legacy-roleplay.com') &&
            !Str::startsWith($back, "https://" . CLUSTER . '.opfw.net') &&
            !Str::startsWith($back, 'http://localhost/')
        ) {
            abort(400);
        }

        SessionHelper::updateCookie($session);

        return redirect($back);
    });
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
    Route::post('/players/{player}/revivePlayer', [PlayerRouteController::class, 'revivePlayer']);
    Route::get('/players/{player}/linked', [PlayerRouteController::class, 'linkedAccounts']);
    Route::delete('/players/{player}/removeIdentifier/{identifier}', [PlayerRouteController::class, 'removeIdentifier']);
    Route::post('/players/{player}/attachScreenshot', [PlayerRouteController::class, 'attachScreenshot']);
    Route::post('/players/{player}/updateTrustedPanelStatus/{status}', [PlayerRouteController::class, 'updateTrustedPanelStatus']);

    // Inventories.
    Route::get('/inventories/character/{character}', [InventoryController::class, 'character']);
    Route::get('/inventories/vehicle/{vehicle}', [InventoryController::class, 'vehicle']);
    Route::get('/inventories/property/{property}', [InventoryController::class, 'property']);
    Route::get('/inventories/motel/{motel}', [InventoryController::class, 'motel']);
    Route::get('/inventories/raw/{identifier}', [InventoryController::class, 'raw']);
    Route::get('/inventory/{inventory}', [InventoryController::class, 'show']);
    Route::post('/inventory/{inventory}/createSnapshot', [InventoryController::class, 'createSnapshot']);
    Route::get('/inventory/snapshot/{snapshot}', [InventoryController::class, 'showSnapshot']);
    Route::get('/inventory_find/{type}/{id}', [InventoryController::class, 'find']);
    Route::delete('/inventory/{inventory}/clear/{slot}', [InventoryController::class, 'clear']);
    Route::get('/search_inventory', [InventoryController::class, 'search']);

    // Advanced search.
    Route::get('/advanced', [AdvancedSearchController::class, 'index']);

    Route::group(['middleware' => ['super-admin']], function () {
        // Blacklisted Identifiers.
        Route::get('/blacklist', [BlacklistController::class, 'index']);
        Route::post('/blacklist', [BlacklistController::class, 'store']);
        Route::delete('/blacklist/{identifier}', [BlacklistController::class, 'destroy']);
    });

    // Suspicious.
    Route::get('/suspicious', [SuspiciousController::class, 'index']);

    // Serials.
    Route::get('/serials', [SerialsController::class, 'index']);

    // Twitter.
    Route::get('twitter', [TwitterController::class, 'index']);
    Route::get('twitter/{user}', [TwitterController::class, 'user']);
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
    Route::post('/players/{player}/characters/{character}/addVehicle', [PlayerCharacterController::class, 'addVehicle']);
    Route::post('/players/{player}/characters/{character}/addLicense', [PlayerCharacterController::class, 'addLicense']);

    // Servers.
    Route::resource('servers', ServerController::class);

    // Map.
    Route::get('/map', [MapController::class, 'index']);
    Route::get('/map/data', [MapController::class, 'data']);

    // Statistics.
    Route::get('/statistics', [StatisticsController::class, 'render']);

    // Overwatch.
    Route::get('/overwatch', [OverwatchController::class, 'index']);

    // Testing.
    Route::post('test/{token}/set_tattoos/{character}/{zone}', [TestController::class, 'setTattoos']);

    // Errors.
    Route::get('/errors/client', [ErrorController::class, 'client']);
    Route::get('/errors/server', [ErrorController::class, 'server']);

    // Exports.
    Route::get('/export/character/{character}', [PlayerCharacterController::class, 'export']);
    Route::get('/export/screenshot/{screenshot}', [PlayerRouteController::class, 'exportScreenshot']);
});

Route::group(['middleware' => ['staff'], 'prefix' => 'api'], function () {
    // Player count api
    Route::get('players', [HomeController::class, 'playerCountApi']);

    // Character info api
    Route::post('characters', [PlayerCharacterController::class, 'getCharacters']);

    // Screenshot api
    Route::post('screenshot/{server}/{id}', [PlayerRouteController::class, 'screenshot']);

    // Overwatch.
    Route::get('randomScreenshot', [OverwatchController::class, 'getRandomScreenshot']);
});

Route::group(['prefix' => 'cron'], function () {
    // ban statistics cronjob
    Route::get('bans', [CronjobController::class, 'updateBanStatistics']);

    // economy statistics cronjob
    Route::get('economy', [CronjobController::class, 'updateEconomyStatistics']);
});

Route::group(['prefix' => 'debug'], function () {
    // log frontend errors
    Route::post('log', function (Request $request) {
        if (!defined('REMOTE_DEBUG') || !REMOTE_DEBUG) {
            abort(401);
        }

        $session = SessionHelper::getInstance();

        $user = $session->get('user') ?? abort(401);
        $username = $user && !empty($user['player']) ? $user['player']['player_name'] : 'N/A';

        $error = $request->json('entry');
        $href = $request->json('href');
        if (!$error || !is_string($error) || !$href || !is_string($href)) {
            abort(400);
        }

        $href = substr($href, 0, 150);
        $error = substr($error, 0, 500);
        $key = $session->getSessionKey();

        $entry = '[' . $key . ' - ' . $username . '] ' . $href . ' - ' . $error;
        $file = storage_path('logs/' . CLUSTER . '_frontend.log');

        file_put_contents($file, $entry . PHP_EOL, FILE_APPEND);
        abort(200);
    });
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
            $file = storage_path('logs/' . CLUSTER . '_op-fw-' . $date . '.log');
            break;
        case 'access':
            $file = storage_path('logs/' . CLUSTER . '_op-fw-access-' . $date . '.log');
            break;
        case 'error':
            $file = storage_path('logs/' . CLUSTER . '_error-' . $date . '.log');
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
