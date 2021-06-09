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

        if (!in_array($player->getOnlineStatus(), [Player::STATUS_ONLINE, Player::STATUS_JOINING])) {
            return back()->with('error', 'Player is offline.');
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
                switch (intval($response['statusCode'])) {
                    case 200:
                        $player->warnings()->create([
                            'issuer_id' => $user->player->user_id,
                            'message'   => 'I kicked this player with the reason: ' . $reason . ' This warning was generated automatically as a result of banning someone.',
                        ]);

                        return back()->with('success', 'Kicked player from the server.');
                    case 401:
                        return back()->with('error', 'Invalid OP-FW configuration.');
                    case 400:
                    case 404:
                        return back()->with('error', 'Failed to kick player from server: "' . (!empty($response['message']) ? $response['message'] : 'Unknown error') . '"');
                }
            }

            return back()->with('error', 'Failed to kick player from server: "Invalid server response"');
        } catch(\Throwable $e) {}

        return back()->with('error', 'Failed to kick player from server.');
    }

}
