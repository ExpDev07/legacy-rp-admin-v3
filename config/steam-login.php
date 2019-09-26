<?php
/**
 * Laravel Steam Login.
 *
 * @link      https://www.maddela.org
 * @link      https://github.com/kanalumaddela/laravel-steam-login
 *
 * @author    kanalumaddela <git@maddela.org>
 * @copyright Copyright (c) 2018-2019 Maddela
 * @license   MIT
 */

return [
    /*
     * Steam API key used for pulling player's profile data
     *
     * @link https://steamcommunity.com/dev/apikey
     *
     * @var string
     */
    'api_key' => env('STEAM_LOGIN_API_KEY', env('STEAM_API_KEY', null)),

    /*
     * Method of retrieving player's profile data.
     * Valid options: xml, api
     *
     * @var string
     */
    'method'  => env('STEAM_LOGIN_PROFILE_DATA_METHOD', env('STEAM_LOGIN_PROFILE_METHOD', 'xml')),

    /*
     * Timeout (seconds) for any requests performed
     *
     * @var int
     */
    'timeout' => env('STEAM_LOGIN_TIMEOUT', 5),

    /*
     * Login/Auth route names/paths
     *
     * @var array
     */
    'routes'  => [
        'login' => env('STEAM_LOGIN_ROUTE', env('STEAM_LOGIN_ROUTE_NAME', 'login.steam')),
        'auth'  => env('STEAM_LOGIN_AUTH_ROUTE', env('STEAM_AUTH_ROUTE_NAME', 'auth.steam')),
    ],
];
