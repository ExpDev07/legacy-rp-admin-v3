<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use kanalumaddela\LaravelSteamLogin\Http\Controllers\AbstractSteamLoginController;
use kanalumaddela\LaravelSteamLogin\SteamUser;

class SteamController extends AbstractSteamLoginController
{

    /**
     * {@inheritdoc}
     */
    public function authenticated(Request $request, SteamUser $steam)
    {
        // Get or create the authenticating user from their steam details.
        $user = $this->get_or_create_user($steam);
        return response()->json($user);
    }

    /**
     * Gets or creates a new user based on the provided steam profile.
     *
     * @param SteamUser $steam The steam profile.
     * @return User
     */
    protected function get_or_create_user(SteamUser $steam)
    {
        // Attempt to find the user and return them if found, otherwise create.
        $user = User::where('account_id', $steam->steamId)->first();
        return $user ? $user : $this->create_user($steam);
    }

    /**
     * Creates a new user from the provided steam profile.
     *
     * @param SteamUser $steam The steam profile.
     * @return User
     */
    protected function create_user(SteamUser $steam)
    {
        // Make sure to fetch the steam user information.
        $steam->getUserInfo();

        return User::create([
            'account_id' => $steam->steamId,
            'name' => $steam->name,
            'avatar' => $steam->avatar
        ]);
    }

}