<?php

namespace Database\Factories;

use App\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'garage_identifier' => $this->faker->streetAddress,
            'model_name'  => $this->faker->firstName . ' ' . $this->faker->year,
            'plate'       => $this->generatePlateNumber(),
        ];
    }

    /**
     * Generates a random plate number.
     *
     * @return string
     */
    protected function generatePlateNumber(): string
    {
        // For example: 28ULD493.
        return Str::upper(
            $this->faker->numberBetween(0, 9) .
            $this->faker->numberBetween(0, 9) .
            $this->faker->randomLetter .
            $this->faker->randomLetter .
            $this->faker->randomLetter .
            $this->faker->numberBetween(0, 9) .
            $this->faker->numberBetween(0, 9) .
            $this->faker->numberBetween(0, 9)
        );
    }

}
