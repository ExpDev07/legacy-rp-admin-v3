<?php

use App\Character;
use App\Player;
use App\User;
use App\Vehicle;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->create()->each(function (User $user) {
            // Create player.
            $player = $user->player()->save(Player::factory()->make([
                'player_name' => $user->name,
            ]));

            // Create three characters.
            for ($i = 1; $i <= 3; $i++) {
                $characters = $player->characters()->save(Character::factory()->make([
                    'character_slot' => $i,
                ]));

                // For each character, create some vehicles.
                $characters->vehicles()->saveMany(Vehicle::factory()->count(5)->make());
            }
        });
    }

}
