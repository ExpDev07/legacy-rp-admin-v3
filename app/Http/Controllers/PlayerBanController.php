<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Http\Requests\BanStoreRequest;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
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
    public function store(Player $player, BanStoreRequest $request)
    {
        // Create a unique hash to go with this player's batch of bans.
        $user = $request->user();
        $hash = Str::uuid()->toString();

        // Create ban.
        $ban = array_merge([
            'ban_hash'     => $hash,
            'creator_name' => $user->player->player_name,
        ], $request->validated());

        // Get identifiers to ban.
        $identifiers = [
            $player->getIdentifier('steam'),
            $player->getIdentifier('license'),
            $player->getIdentifier('discord'),
            $player->getIdentifier('xbl'),
            $player->getIdentifier('live'),
            // $player->getIdentifier('ip'),
        ];

        // Go through the player's identifiers and create a ban record for each of them.
        Collection::make($identifiers)
            ->filter()
            ->each(fn ($identifier) => $player->bans()->updateOrCreate([ 'identifier' => $identifier ], $ban));

        // Create reason.
        $reason = $request->input('reason')
            ? 'I banned this person with the reason: ' . $request->input('reason') . '.'
            : 'I banned this person without a reason.';

        // Automatically log the ban as a warning.
        $player->warnings()->create([
            'issuer_id' => $user->player->user_id,
            'message'   => $reason . ' This warning was generated automatically as a result of banning someone.',
        ]);

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
