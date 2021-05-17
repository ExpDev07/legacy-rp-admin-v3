<?php

namespace Database\Factories;

use App\Player;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

class PlayerFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Player::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // The identifier to assign player.
        $identifier = 'steam:' . $this->faker->uuid;

        // 30% to get super admin and this->60% to get staff in general.
        $is_super_admin = $this->faker->boolean(30);
        $is_staff       = $is_super_admin || $this->faker->boolean(60);

        return [
            'steam_identifier' => $identifier,
            'player_name'      => $this->faker->name,
            'is_staff'         => $is_staff,
            'is_super_admin'   => $is_super_admin,
            'is_soft_banned'   => false,
            'playtime'         => $this->faker->numberBetween(0, 10000),
            'total_joins'      => $this->faker->numberBetween(0, 1000),
            'priority_level'   => $this->faker->numberBetween(0, 10),
            'last_connection'  => now(),
            'identifiers'      => [
                $identifier,
                'ip:' . $this->faker->ipv4,
            ],
        ];
    }

}
