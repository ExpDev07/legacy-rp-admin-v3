<?php

/** @var Factory $factory */

use App\Player;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Date;

$factory->define(Player::class, function (Faker $faker) {

    // The identifier to assign player.
    $identifier = 'steam:' . $faker->uuid;

    // 30% to get super admin and 60% to get staff in general.
    $is_super_admin = $faker->boolean(30);
    $is_staff       = $is_super_admin || $faker->boolean(60);

    return [
        'steam_identifier' => $identifier,
        'player_name'      => $faker->name,
        'is_staff'         => $is_staff,
        'is_super_admin'   => $is_super_admin,
        'playtime'         => $faker->numberBetween(0, 10000),
        'last_connection'  => Date::now(),
        'identifiers'      => [
            $identifier,
            'ip:' . $faker->ipv4,
        ],
    ];
});
