<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Http\Requests\BanStoreRequest;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class PlayerBanController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param BanStoreRequest $request
     * @return RedirectResponse
     */
    public function store(Player $player, BanStoreRequest $request): RedirectResponse
    {
        // Create a unique hash to go with this player's batch of bans
        $user = $request->user();
        $hash = Str::uuid()->toString();

        // Get identifiers to ban.
        $identifiers = [
            $player->getIdentifier('steam'),
            $player->getIdentifier('license'),
            $player->getIdentifier('discord'),
            $player->getIdentifier('xbl'),
            $player->getIdentifier('live'),
            $player->getIdentifier('ip'),
        ];

        // Go through the player's identifiers and create a ban record for each of them.
        foreach ($identifiers as $identifier) {
            if ($identifier === null) {
                continue;
            }

            // Create ban.
            $ban = array_merge([
                'ban_hash' => $hash,
                'creator_name' => $user->player->player_name,
            ], $request->validated());

            // Save ban.
            $player->bans()->updateOrCreate([ 'identifier' => $identifier ], $ban);
        }

        return back()->with('success', 'The player has successfully been banned.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Ban $ban
     * @return RedirectResponse
     */
    public function destroy(Player $player, Ban $ban): RedirectResponse
    {
        $player->bans()->forceDelete();
        return back()->with('success', 'The player has successfully been unbanned.');
    }

}
