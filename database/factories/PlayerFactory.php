<?php

/** @var Factory $factory */

use App\Player;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Player::class, function (Faker $faker) {
    return [
        'name'        => $faker->name,
        'identifier'  => $faker->uuid,
        'staff'       => $faker->boolean,
        'identifiers' => [],
    ];
});
