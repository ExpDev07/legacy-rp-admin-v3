<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarningStoreRequest;
use App\Player;
use App\Warning;
use Illuminate\Http\RedirectResponse;

class PlayerWarningController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param WarningStoreRequest $request
     * @return RedirectResponse
     */
    public function store(Player $player, WarningStoreRequest $request): RedirectResponse
    {
        $player->warnings()->create(array_merge($request->validated(), [
            'issuer_id' => $request->user()->player->user_id,
        ]));
        return back()->with('success', 'The player has successfully been warned.');
    }

    /**
     * Updates the specified resource.
     *
     * @param Player $player
     * @param Warning $warning
     * @param WarningStoreRequest $request
     * @return RedirectResponse
     */
    public function update(Player $player, Warning $warning, WarningStoreRequest $request): RedirectResponse
    {
        $staffIdentifier = $request->user()->player->steam_identifier;
        $issuer = $warning->issuer()->first();
        if (!$issuer || $staffIdentifier !== $issuer->steam_identifier) {
            return back()->with('error', 'You can only edit your own warnings!');
        }

        $warning->update($request->validated());

        return back()->with('success', 'Successfully updated warning');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Warning $warning
     * @return RedirectResponse
     */
    public function destroy(Player $player, Warning $warning)
    {
        $warning->forceDelete();
        return back()->with('success', 'The warning has successfully been deleted from the player\'s record.');
    }

}
