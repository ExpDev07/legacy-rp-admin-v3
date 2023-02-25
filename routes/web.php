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
use App\Http\Controllers\CasinoLogController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\CronjobController;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SteamLookupController;
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
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ScreenshotController;
use App\Http\Controllers\SerialsController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\StaffChatController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SuspiciousController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TwitterController;
use App\Http\Controllers\LoadingScreenController;
use App\Http\Controllers\WeaponController;
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

// Routes requiring being logged in as a staff member.
Route::group(['middleware' => ['log', 'staff']], function () {
    // Home.
    Route::get('/', [HomeController::class, 'render']);
    Route::get('/changelog', [ChangelogController::class, 'render']);

	// Steam Lookup.
    Route::get('/steam', [SteamLookupController::class, 'render']);
    Route::post('/steam', [SteamLookupController::class, 'playerInfo']);

    Route::get('/staff', [StaffChatController::class, 'staff']);
    Route::post('/staffChat', [StaffChatController::class, 'externalStaffChat']);

    // Players.
    Route::get('/players', [PlayerController::class, 'index']);
    Route::get('/players/{player}', [PlayerController::class, 'show']);
    Route::resource('players.characters', PlayerCharacterController::class);
    Route::resource('players.bans', PlayerBanController::class);
    Route::resource('players.warnings', PlayerWarningController::class);
    Route::post('/players/{player}/kick', [PlayerRouteController::class, 'kick']);
    Route::post('/players/{player}/staffPM', [PlayerRouteController::class, 'staffPM']);
    Route::post('/players/{player}/unloadCharacter', [PlayerRouteController::class, 'unloadCharacter']);
    Route::post('/players/{player}/revivePlayer', [PlayerRouteController::class, 'revivePlayer']);
    Route::get('/players/{player}/linked', [PlayerRouteController::class, 'linkedAccounts']);
    Route::get('/players/{player}/discord', [PlayerRouteController::class, 'discordAccounts']);
    Route::get('/players/{player}/antiCheat', [PlayerRouteController::class, 'antiCheat']);
    Route::get('/players/{player}/screenshots', [PlayerRouteController::class, 'screenshots']);
    Route::get('/players/{player}/panelLogs', [PlayerRouteController::class, 'panelLogs']);
    Route::get('/players/{player}/status', [PlayerRouteController::class, 'status']);
    Route::delete('/players/{player}/removeIdentifier/{identifier}', [PlayerRouteController::class, 'removeIdentifier']);
    Route::post('/players/{player}/attachScreenshot', [PlayerRouteController::class, 'attachScreenshot']);
    Route::post('/players/{player}/updateSoftBanStatus/{status}', [PlayerRouteController::class, 'updateSoftBanStatus']);
    Route::post('/players/{player}/updateTag', [PlayerRouteController::class, 'updateTag']);
    Route::post('/players/{player}/updateRole', [PlayerRouteController::class, 'updateRole']);
    Route::post('/players/{player}/updateEnabledCommands', [PlayerRouteController::class, 'updateEnabledCommands']);

    Route::post('/players/{player}/bans/{ban}/lock', [PlayerBanController::class, 'lockBan']);
    Route::post('/players/{player}/bans/{ban}/unlock', [PlayerBanController::class, 'unlockBan']);

    Route::get('/new_players', [PlayerController::class, 'newPlayers']);
    Route::get('/linked_ips', [PlayerBanController::class, 'linkedIPs']);
    Route::get('/linked_tokens', [PlayerBanController::class, 'linkedTokens']);
    Route::get('/backstories', [PlayerCharacterController::class, 'backstories']);
    Route::get('/api/backstories', [PlayerCharacterController::class, 'backstoriesApi']);

    Route::get('/bans', [PlayerBanController::class, 'index']);
    Route::get('/my_bans', [PlayerBanController::class, 'indexMine']);
    Route::get('/system_bans', [PlayerBanController::class, 'indexSystem']);

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
    Route::get('/inventory/item/{id}', [InventoryController::class, 'itemHistory']);

    // Advanced search.
    Route::get('/advanced', [AdvancedSearchController::class, 'index']);

    Route::group(['middleware' => ['super-admin']], function () {
        // Blacklisted Identifiers.
        Route::get('/blacklist', [BlacklistController::class, 'index']);
        Route::post('/blacklist', [BlacklistController::class, 'store']);
        Route::delete('/blacklist/{identifier}', [BlacklistController::class, 'destroy']);

        Route::post('/blacklist/import', [BlacklistController::class, 'import']);

        // Loading screen pictures
        Route::get('/loading_screen', [LoadingScreenController::class, 'index']);
        Route::delete('/loading_screen/{id}', [LoadingScreenController::class, 'delete']);
        Route::post('/loading_screen', [LoadingScreenController::class, 'add']);
        Route::put('/loading_screen/{id}', [LoadingScreenController::class, 'edit']);
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
    Route::get('/searches', [LogController::class, 'searches']);
    Route::get('/phoneLogs', [LogController::class, 'phoneLogs']);

    // Casino Logs.
    Route::resource('casino', CasinoLogController::class);

    // Panel Logs.
    Route::resource('panel_logs', PanelLogController::class);

    // Characters.
    Route::resource('characters', PlayerCharacterController::class);
    Route::post('vehicles/delete/{vehicle}', [PlayerCharacterController::class, 'deleteVehicle']);
    Route::post('vehicles/edit/{vehicle}', [PlayerCharacterController::class, 'editVehicle']);
    Route::post('vehicles/resetGarage/{vehicle}/{fullReset}', [PlayerCharacterController::class, 'resetGarage']);
    Route::post('/players/{player}/characters/{character}/removeTattoos', [PlayerCharacterController::class, 'removeTattoos']);
    Route::post('/players/{player}/characters/{character}/resetSpawn', [PlayerCharacterController::class, 'resetSpawn']);
    Route::put('/players/{player}/characters/{character}/editBalance', [PlayerCharacterController::class, 'editBalance']);
    Route::post('/players/{player}/characters/{character}/addVehicle', [PlayerCharacterController::class, 'addVehicle']);
    Route::post('/players/{player}/characters/{character}/addLicense', [PlayerCharacterController::class, 'addLicense']);

    // Servers.
    Route::resource('servers', ServerController::class);

    // Map.
    Route::get('/map', [MapController::class, 'index']);
    Route::post('/map/playerNames', [MapController::class, 'playerNames']);
    Route::get('/map/noclipBans', [MapController::class, 'noclipBans']);

    // Statistics.
    Route::get('/statistics', [StatisticsController::class, 'render']);

    // Overwatch.
    Route::get('/overwatch', [OverwatchController::class, 'index']);

    // Screenshots.
    Route::get('/screenshots', [ScreenshotController::class, 'render']);
    Route::get('/anti_cheat', [ScreenshotController::class, 'antiCheat']);

    Route::get('/cheat/{type}', [ScreenshotController::class, 'docs']);

    // Errors.
    Route::get('/errors/client', [ErrorController::class, 'client']);
    Route::post('/errors/client/cycle', [ErrorController::class, 'clientCycle']);

    Route::get('/errors/server', [ErrorController::class, 'server']);
    Route::post('/errors/server/cycle', [ErrorController::class, 'serverCycle']);

    // Exports.
    Route::get('/export/character/{character}', [PlayerCharacterController::class, 'export']);
    Route::get('/export/screenshot/{screenshot}', [PlayerRouteController::class, 'exportScreenshot']);

    // Queue.
    Route::get('/queue/{server}', [QueueController::class, 'render']);
    Route::post('/skip_queue/{server}/{licenseIdentifier}', [QueueController::class, 'skip']);
    Route::get('/api/queue/{server}', [QueueController::class, 'api']);

    Route::get('/test/logs/{action}', [TestController::class, 'logs']);
    Route::get('/test/smart_watch', [TestController::class, 'smartWatchLeaderboard']);
    Route::get('/test/bans', [TestController::class, 'banLeaderboard']);
    Route::get('/test/system', [TestController::class, 'systemBans']);
    Route::get('/test/modders', [TestController::class, 'moddingBans']);
    Route::get('/test/staff', [TestController::class, 'staffPlaytime']);
    Route::get('/test/finance', [TestController::class, 'finance']);

    Route::get('/weapon/{weapon}', [WeaponController::class, 'weaponDamage']);
});

Route::group(['middleware' => ['staff'], 'prefix' => 'api'], function () {
    // Player count api
    Route::get('players', [HomeController::class, 'playerCountApi']);

    // Character info api
    Route::post('characters', [PlayerCharacterController::class, 'getCharacters']);

    // Screenshot api
    Route::post('screenshot/{server}/{id}', [PlayerRouteController::class, 'screenshot']);
    Route::post('capture/{server}/{id}/{duration}', [PlayerRouteController::class, 'capture']);

    // Overwatch.
    Route::get('randomScreenshot', [OverwatchController::class, 'getRandomScreenshot']);
});

Route::group(['prefix' => 'cron'], function () {
    // General purpose cronjobs
    Route::get('general', [CronjobController::class, 'generalCronjob']);
});

Route::group(['prefix' => 'debug'], function () {
    // log frontend errors
    Route::post('log', function (Request $request) {
        if (true) {
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

Route::get('/test/job_api/{api_key}/{jobName}/{departmentName}/{positionName}/{characterIds}', [TestController::class, 'jobApi']);

Route::get('/test/hello', function() {
    return (new Response("Hello", 200))
            ->header('Content-Type', 'text/plain');
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

if (!function_exists('doOPLogFileDownload')) {
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
}
