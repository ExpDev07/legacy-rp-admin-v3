<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use CloudCreativity\LaravelJsonApi\Routing\RouteRegistrar;
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

// https://laravel-json-api.readthedocs.io/en/latest/basics/routing/
// https://laravel.com/docs/5.8/api-authentication#protecting-routes

// Register the default API routes. Make sure to protect them as well. The "auth:api" middleware will check for a valid
// access_token (referred to as "api_token" in Laravel).
JsonApi::register('default')->middleware('auth:api')->routes(function (RouteRegistrar $api, $router) {
    // Users resource.
    $api->resource('users')->relationships(function ($relations) {
        $relations->hasOne('player');
    });

    // Players resource.
    $api->resource('players')->relationships(function ($relations) {
        $relations->hasOne('user');
    });

    $router->get('testing', function () {
       return response()->json('test');
    });
});