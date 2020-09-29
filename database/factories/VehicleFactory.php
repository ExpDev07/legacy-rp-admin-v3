<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Vehicle;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Vehicle::class, function (Faker $faker) {
    return [
        'garage_name' => $faker->streetAddress,
        'model_name' => $faker->firstName . ' ' . $faker->year,
        'plate' => generatePlateNumber($faker),
    ];
});

/**
 * Generates a random plate number.
 *
 * @param Faker $faker
 * @return string
 */
function generatePlateNumber(Faker $faker): string
{
    // For example: 28ULD493.
    return Str::upper(
        $faker->numberBetween(0, 9) .
        $faker->numberBetween(0, 9) .
        $faker->randomLetter .
        $faker->randomLetter .
        $faker->randomLetter .
        $faker->numberBetween(0, 9) .
        $faker->numberBetween(0, 9) .
        $faker->numberBetween(0, 9)
    );
}
