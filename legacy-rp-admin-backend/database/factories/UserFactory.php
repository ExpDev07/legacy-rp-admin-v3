<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;

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
        'account_id' => $faker->numberBetween(10000000000, 100000000000),
        'name' => $faker->firstName . ' ' . $faker->lastName,
        'avatar' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/e1/e139eec729031ced290d1130f164622de73c3f7a_full.jpg',
        'api_token' => $faker->uuid
    ];
});
