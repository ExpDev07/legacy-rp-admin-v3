<?php

namespace App\Http\Controllers\Auth;

use App\Player;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use kanalumaddela\LaravelSteamLogin\Http\Controllers\AbstractSteamLoginController;
use kanalumaddela\LaravelSteamLogin\SteamUser;

/**
 * A controller to authenticate with steam.
 *
 * @package App\Http\Controllers\Auth
 */
class SteamController extends AbstractSteamLoginController
{

    /**
     * {@inheritdoc}
     */
    public function authenticated(Request $request, SteamUser $steam)
    {
        // Make sure to fetch the steam user information.
        $steam->getUserInfo();

        // Create the user or update them if already exists.
        $user = User::query()->updateOrCreate([
            'account_id' => $steam->steamId,
            'name' => $steam->name,
            'avatar' => $steam->avatar,
        ]);

        $player = Player::query()
            ->where('steam_identifier', '=', 'steam:' . dechex(intval($steam->steamId)))
            ->first()
            ->toArray();

        $user = $user->toArray();
        $user['player'] = $player;
        $user['player']['avatar'] = $user['avatar'];

        session()->put('user', $user);

        return redirect('/');
    }

}
