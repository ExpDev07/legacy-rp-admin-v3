<?php

namespace App\Http\Controllers\Player;

use App\Ban;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBanRequest;
use App\Player;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class BanController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param StoreBanRequest $request
     * @return Response
     */
    public function store(Player $player, StoreBanRequest $request)
    {
        // Go through the player's identifiers and create a ban record for each of them.
        foreach ($player->getIdentifiers() as $identifier) {

            $player->bans()->updateOrCreate([ 'identifier' => $identifier ], array_merge($request->validated(), [
                'ban-id'     => Str::uuid()->toString(),
                'banner-id'  => auth()->user()->player->staff,
            ]));

        }
        return back();
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
        // Remove all of the player's bans.
        $player->bans()->forceDelete();
        return back();
    }
}
