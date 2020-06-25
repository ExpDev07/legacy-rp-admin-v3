<?php

use App\Character;
use App\Player;
use App\User;
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
        factory(User::class, 10)->create()->each(function (User $user) {
            // Create player.
            $player = $user->player()->save(factory(Player::class)->make([
                'player_name' => $user->name
            ]));

            // Create three characters.
            for ($i = 1; $i <= 3; $i++) {
                $player->characters()->save(factory(Character::class)->make([
                    'character_slot' => $i,
                ]));
            }
        });
    }

}
