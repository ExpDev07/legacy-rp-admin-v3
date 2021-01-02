<?php

namespace Database\Factories;

use App\Ban;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
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
            'ban_hash'  => $this->faker->uuid,
            'reason'    => $this->faker->sentence,
            'expire'    => Arr::random([null, Date::createFromTimestampMs(0)->addWeek()->getTimestamp() ]),
            'timestamp' => Date::now(),
        ];
    }

}
