<?php

namespace App\Http\Controllers;

use App\Helpers\OPFWHelper;
use App\Http\Requests\KickStoreRequest;
use App\Player;
use Illuminate\Http\RedirectResponse;

class PlayerRouteController extends Controller
{

    /**
     * Kick a player from the game
     *
     * @param Player $player
     * @param KickStoreRequest $request
     * @return RedirectResponse
     */
    public function kick(Player $player, KickStoreRequest $request): RedirectResponse
    {
        $user = $request->user();
        $reason = $request->input('reason') ?: 'You were kicked by ' . $user->player->player_name;

        return OPFWHelper::kickPlayer($user->player->steam_identifier, $player, $reason)->redirect();
    }

    /**
     * Send a staffPM to a player
     *
     * @param Player $player
     * @param KickStoreRequest $request
     * @return RedirectResponse
     */
    public function staffPM(Player $player, KickStoreRequest $request): RedirectResponse
    {
        $user = $request->user();
        $message = trim($request->input('message'));

        return OPFWHelper::staffPM($user->player->steam_identifier, $player, $message)->redirect();
    }

}
