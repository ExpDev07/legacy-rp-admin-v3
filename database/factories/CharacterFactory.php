<?php

namespace Database\Factories;

use App\Character;
use Illuminate\Database\Eloquent\Factory;

class CharacterFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Character::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'character_slot' => $this->faker->numberBetween(1, 3),
            'gender'         => $this->faker->randomElement([ 'male', 'female', 'non-binary' ]),
            'first_name'     => $this->faker->firstName,
            'last_name'      => $this->faker->lastName,
            'date_of_birth'  => $this->faker->date(),
            'cash'           => $this->faker->randomFloat(2, 100, 5000000),
            'bank'           => $this->faker->randomFloat(2, 100, 10000000),
            'job_name'       => $this->faker->jobTitle,
            'backstory'      => $this->faker->text,
        ];
    }

}
