<?php

namespace App\Http\Controllers;

use App\Helpers\OPFWHelper;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlayerRouteController extends Controller
{

    /**
     * Kick a player from the game
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function kick(Player $player, Request $request): RedirectResponse
    {
        if (empty(trim($request->input('reason')))) {
            return back()->with('error', 'Reason cannot be empty');
        }

        $user = $request->user();
        $reason = $request->input('reason') ?: 'You were kicked by ' . $user->player->player_name;

        return OPFWHelper::kickPlayer($user->player->steam_identifier, $user->player->player_name, $player, $reason)->redirect();
    }

    /**
     * Send a staffPM to a player
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function staffPM(Player $player, Request $request): RedirectResponse
    {
        $user = $request->user();
        $message = trim($request->input('message'));

        if (empty($message)) {
            return back()->with('error', 'Message cannot be empty');
        }

        return OPFWHelper::staffPM($user->player->steam_identifier, $player, $message)->redirect();
    }

    /**
     * Unload someones character
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function unloadCharacter(Player $player, Request $request): RedirectResponse
    {
        $user = $request->user();
        $character = trim($request->input('character'));

        if (empty($character)) {
            return back()->with('error', 'Character ID cannot be empty');
        }

        return OPFWHelper::unloadCharacter($user->player->steam_identifier, $player, $character)->redirect();
    }

}
