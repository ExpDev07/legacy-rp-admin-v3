<?php

/** @var Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'account_id' => $faker->numberBetween(1000000, 1000000),
        'name' => $faker->firstName . ' ' . $faker->lastName,
        'avatar' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/e1/e139eec729031ced290d1130f164622de73c3f7a_full.jpg',
    ];
});
