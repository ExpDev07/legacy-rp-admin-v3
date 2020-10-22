<?php

namespace Database\Factories;

use App\Ban;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Date;

class BanFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ban::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ban_hash' => $this->faker->uuid,
            'reason' => $this->faker->sentence,
            'timestamp' => Date::now(),
            'expire' => Date::now()->addMonth(),
        ];
    }

}
