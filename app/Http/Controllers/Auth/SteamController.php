<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
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
        $user = User::updateOrCreate([
            'account_id' => $steam->steamId,
            'name' => $steam->name,
            'avatar' => $steam->avatar,
        ]);

        // Log them in!
        auth()->login($user, true);
    }

}
