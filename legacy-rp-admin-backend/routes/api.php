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

// Register the default API routes.
JsonApi::register('default')->routes(function (RouteRegistrar $api) {
    // Users resource.
    $api->resource('users')->relationships(function ($relations) {
        $relations->hasOne('player');
    });

    // Players resource.
    $api->resource('players')->relationships(function ($relations) {
        $relations->hasOne('user');
    });
});