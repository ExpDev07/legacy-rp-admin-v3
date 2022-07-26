<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LoggingHelper;
use App\Helpers\SessionHelper;
use App\Http\Middleware\StaffMiddleware;
use App\Player;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        LoggingHelper::log($session->getSessionKey(), 'Loading player');
        $start = round(microtime(true) * 1000);

        $player = Player::query()
            ->where('steam_identifier', '=', 'steam:' . dechex(intval($steam->steamId)))
            ->first();

        $end = round(microtime(true) * 1000);
        LoggingHelper::log($session->getSessionKey(), 'Player loaded in ' . ($end - $start) . 'ms');

        $user = $user->toArray();

        if (array_key_exists('avatar', $user) && empty($user['avatar'])) {
            LoggingHelper::log($session->getSessionKey(), 'Setting default avatar');
            $user['avatar'] = '/images/op-logo.png';
        }

        $redirect = '/';

        if ($player && !empty($user['avatar'])) {
            $user['player'] = $player->toArray();
            $user['player']['avatar'] = $user['avatar'];

            LoggingHelper::log($session->getSessionKey(), 'Putting user in session');
            $session->put('user', $user);

            // Session lock update
            $session->put('session_lock', StaffMiddleware::getSessionDetail());
            $session->put('session_detail', StaffMiddleware::getFingerprint());
            $session->put('last_updated', time());

            StaffMiddleware::updateSessionLock();

            if ($session->exists('returnTo')) {
                $redirect = $session->get('returnTo');
            }
        } else {
            LoggingHelper::log($session->getSessionKey(), 'Failed to create login session {player:' . json_encode(!!$player) . ', user->avatar:' . json_encode(!empty($user['avatar'])) . '}');
            LoggingHelper::log($session->getSessionKey(), 'user->' . json_encode([
                    'account_id' => $steam->steamId,
                    'identifier' => 'steam:' . dechex(intval($steam->steamId)),
                    'name'       => $steam->name,
                    'avatar'     => $steam->avatar,
                ]));
            LoggingHelper::log($session->getSessionKey(), 'steam->' . json_encode($steam->toArray()));
            LoggingHelper::log($session->getSessionKey(), 'player->' . json_encode($player ? $player->toArray() : null));

            return redirect('/login')->with('error', !$player ? 'You have to have connected to the server at least once before trying to log-in (Player not found).' : 'Failed to get information from steam, please contact a developer.');
        }

        $host = $_SERVER['HTTP_HOST'];
        switch ($host) {
            case CLUSTER . '.legacy-roleplay.com':
                $redirect = 'https://' . CLUSTER . '.opfw.net/s/' . $session->getSessionKey() . '?back=' . urlencode('https://' . $host . $redirect);
                break;
            case CLUSTER . '.opfw.net':
                $redirect = 'https://' . CLUSTER . '.legacy-roleplay.com/s/' . $session->getSessionKey() . '?back=' . urlencode('https://' . $host . $redirect);
                break;
            case 'localhost':
                $redirect = 'http://localhost/s/' . $session->getSessionKey() . '?back=' . urlencode('http://localhost' . $redirect);
                break;
        }

        return redirect($redirect);
    }

}
