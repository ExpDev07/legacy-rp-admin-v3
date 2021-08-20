<?php

namespace App\Http\Controllers;

use App\Helpers\OPFWHelper;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

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

    /**
     * Returns all linked accounts
     *
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function linkedAccounts(Player $player, Request $request): Response
    {
        $identifiers = $player->getIdentifiers();
        $linked = [
            'total'  => 0,
            'linked' => [],
        ];

        foreach ($identifiers as $identifier) {
            if (Str::startsWith($identifier, "ip:")) {
                continue;
            }

            if (!isset($linked['linked'][$identifier])) {
                $linked['linked'][$identifier] = [
                    'label'    => Player::getIdentifierLabel($identifier),
                    'accounts' => [],
                ];
            }

            $accounts = Player::query()
                ->where('identifiers', 'LIKE', '%' . $identifier . '%')
                ->where('steam_identifier', '!=', $player->steam_identifier)
                ->select(['steam_identifier', 'player_name'])
                ->get()->toArray();
            $linked['linked'][$identifier]['accounts'] = $accounts;

            $linked['total'] += sizeof($accounts);
        }

        return (new Response([
            'status' => true,
            'data'   => $linked,
        ], 200))->header('Content-Type', 'application/json');
    }

}
