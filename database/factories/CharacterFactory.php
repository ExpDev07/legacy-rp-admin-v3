<?php

/** @var Factory $factory */

use App\Character;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Character::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstName,
        'lastname'  => $faker->lastName,
        'gender'    => $faker->randomElement([ 'male', 'female', 'non-binary' ]),
        'dob'       => $faker->date(),
        'height'    => $faker->numberBetween(150, 200),
        'cash'      => $faker->randomFloat(2, 100, 5000000),
        'bank'      => $faker->randomFloat(2, 100, 10000000),
        'job'       => $faker->jobTitle,
        'story'     => $faker->text,

        'basicneeds' => [],
        'licenses'   => [],
        'model'      => [],
        'tattoos'    => [],
        'ammo'       => [],
        'animations' => []
    ];
});
