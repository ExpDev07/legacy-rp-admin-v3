<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LoggingHelper;
use App\Helpers\SessionHelper;
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
        $session = SessionHelper::getInstance();

        LoggingHelper::log($session->getSessionKey(), 'Login triggered, getting user info');
        // Make sure to fetch the steam user information.
        $steam->getUserInfo();

        LoggingHelper::log($session->getSessionKey(), 'Updating user in table');
        // Create the user or update them if already exists.
        $user = User::query()->updateOrCreate([
            'account_id' => $steam->steamId,
            'name'       => $steam->name,
            'avatar'     => $steam->avatar,
        ]);

        LoggingHelper::log($session->getSessionKey(), json_encode([
            'account_id' => $steam->steamId,
            'name'       => $steam->name,
            'avatar'     => $steam->avatar,
        ]));

        LoggingHelper::log($session->getSessionKey(), 'Loading player');
        $player = Player::query()
            ->where('steam_identifier', '=', 'steam:' . dechex(intval($steam->steamId)))
            ->first()
            ->toArray();

        $user = $user->toArray();

        if (array_key_exists('avatar', $user) && empty($user['avatar'])) {
            LoggingHelper::log($session->getSessionKey(), 'Setting default avatar');
            $user['avatar'] = '/images/op-logo.png';
        }

        if ($player && !empty($user['avatar'])) {
            $user['player'] = $player;
            $user['player']['avatar'] = $user['avatar'];

            LoggingHelper::log($session->getSessionKey(), 'Putting user in session');
            $session->put('user', $user);
        } else {
            LoggingHelper::log($session->getSessionKey(), 'Failed to create login session {player:' . json_encode(!!$player) . ', user->avatar:' . json_encode(!empty($user['avatar'])) . '}');
        }

        return redirect('/');
    }

}
