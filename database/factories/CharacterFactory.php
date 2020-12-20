<?php

namespace Database\Factories;

use App\Character;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'character_slot'  => $this->faker->numberBetween(1, 3),
            'gender'          => $this->faker->randomElement([ 'male', 'female', 'non-binary' ]),
            'first_name'      => $this->faker->firstName,
            'last_name'       => $this->faker->lastName,
            'date_of_birth'   => $this->faker->date(),
            'cash'            => $this->faker->randomNumber(5),
            'bank'            => $this->faker->randomNumber(5),
            'stocks_balance'  => $this->faker->randomNumber(5),
            'job_name'        => $this->faker->jobTitle,
            'department_name' => $this->faker->randomElement([ 'office', 'field', 'relations' ]),
            'position_name'   => $this->faker->randomElement([ 'manager', 'assistant', 'employee' ]),
            'backstory'       => $this->faker->text,
        ];
    }

}
