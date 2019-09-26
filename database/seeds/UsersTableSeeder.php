<?php

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
            $user->player()->create([
                'identifier' => $user->identifier,
                'name' => $user->name
            ]);
        });
    }
}
