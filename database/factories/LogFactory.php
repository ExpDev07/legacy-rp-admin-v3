<?php

/** @var Factory $factory */

use App\Log;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Log::class, function (Faker $faker) {
    return [
        'action'   => 'example-action',
        'details'  => $faker->sentence,
        'metadata' => []
    ];
});
