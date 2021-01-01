<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'account_id' => $this->faker->unixTime,
            'name'       => $this->faker->firstName . ' ' . $this->faker->lastName,
            'avatar'     => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/e1/e139eec729031ced290d1130f164622de73c3f7a_full.jpg',
        ];
    }

}
