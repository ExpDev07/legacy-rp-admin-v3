<?php

/** @var Factory $factory */

use App\Player;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Player::class, function (Faker $faker) {

    // The identifier to assign player.
    $identifier = $faker->uuid;

    // Create the player's staff status.
    $staff = $faker->boolean(35) ? 'M-' . $faker->uuid : null;

    return [
        'name'        => $faker->name,
        'playtime'    => $faker->numberBetween(0, 10000),
        'identifier'  => $identifier,
        'staff'       => $staff,
        'identifiers' => [
            $faker->ipv4, $identifier
        ],
    ];
});
