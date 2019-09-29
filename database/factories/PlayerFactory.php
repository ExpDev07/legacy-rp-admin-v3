<?php

/** @var Factory $factory */

use App\Player;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Player::class, function (Faker $faker) {

    // Create the player's staff status.
    $staff = $faker->boolean(35) ? 'M-' . $faker->uuid : null;

    return [
        'name'        => $faker->name,
        'identifier'  => $faker->uuid,
        'playtime'    => $faker->numberBetween(0, 10000),
        'staff'       => $staff,
        'identifiers' => [],
    ];
});
