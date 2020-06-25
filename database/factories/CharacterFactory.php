<?php

/** @var Factory $factory */

use App\Character;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Character::class, function (Faker $faker) {
    return [
        'character_slot' => $faker->numberBetween(1, 3),
        'gender'         => $faker->randomElement([ 'male', 'female', 'non-binary' ]),
        'first_name'     => $faker->firstName,
        'last_name'      => $faker->lastName,
        'date_of_birth'  => $faker->date(),
        'cash'           => $faker->randomFloat(2, 100, 5000000),
        'bank'           => $faker->randomFloat(2, 100, 10000000),
        'job_name'       => $faker->jobTitle,
        'backstory'      => $faker->text,
    ];
});
