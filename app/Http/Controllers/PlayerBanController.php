<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Http\Requests\BanStoreRequest;
use App\Player;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class PlayerBanController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param BanStoreRequest $request
     * @return Response
     */
    public function store(Player $player, BanStoreRequest $request)
    {
        // Create a unique hash to go with this player's batch of bans.
        $hash = Str::uuid()->toString();

        // Go through the player's identifiers and create a ban record for each of them.
        foreach ($player->getIdentifiers() as $identifier) {
            $player->bans()->updateOrCreate(['identifier' => $identifier], array_merge($request->validated(), [
                'ban_hash' => $hash,
                'creator_name' => $request->user()->player->player_name,
            ]));
        }

        return back()->with('success', 'The player has successfully been banned.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Ban $ban
     * @return Response
     */
    public function destroy(Player $player, Ban $ban)
    {
        $player->bans()->forceDelete();
        return back()->with('success', 'The player has successfully been unbanned.');
    }

}
