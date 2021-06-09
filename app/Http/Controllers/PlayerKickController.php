<?php

namespace App\Http\Controllers;

use App\Http\Requests\KickStoreRequest;
use App\Player;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;

class PlayerKickController extends Controller
{

    /**
     * Kick a player from the game
     *
     * @param Player $player
     * @param KickStoreRequest $request
     * @return RedirectResponse
     */
    public function store(Player $player, KickStoreRequest $request): RedirectResponse
    {
        $url = env('OP_FW_SERVER');
        $token = env('OP_FW_TOKEN');

        if (!$url || !$token) {
            return back()->with('error', 'Missing OP-FW configuration.');
        }

        $user = $request->user();
        $steam = $player->getSteamID();
        $reason = $request->input('reason') ?: 'You were kicked by ' . $user->player->player_name;

        try {
            $client = new Client();
            $res = $client->request('GET', 'https://' . $url . '/op-framework/execute/kickPlayer', [
                'query' => [
                    'steamIdentifier'         => $steam,
                    'reason'                  => $reason,
                    'removeReconnectPriority' => false
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            $response = json_decode($res->getBody()->getContents(), true);
            if ($response) {
                return back()->with('success', 'Kicked player from the server.');
            }
        } catch(\Throwable $e) {}

        return back()->with('error', 'Failed to kick player from server.');
    }

}
