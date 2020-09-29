<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarningStoreRequest;
use App\Player;
use App\Warning;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class PlayerWarningController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param WarningStoreRequest $request
     * @return RedirectResponse
     */
    public function store(Player $player, WarningStoreRequest $request)
    {
        $player->warnings()->create(array_merge($request->validated(), [
            'issuer_id' => $request->user()->player->user_id,
        ]));
        return back()->with('success', 'The player has successfully been warned.');
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
