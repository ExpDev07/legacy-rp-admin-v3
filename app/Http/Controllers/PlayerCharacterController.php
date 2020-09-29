<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Requests\CharacterUpdateRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\PlayerResource;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Http\Response;

class PlayerCharacterController extends Controller
{

    /**
     * Display the specified resource for editing.
     *
     * @param Player $player
     * @param Character $character
     * @return \Inertia\Response
     */
    public function edit(Player $player, Character $character)
    {
        return Inertia::render('Players/Characters/Edit', [
            'player' => new PlayerResource($player),
            'character' => new CharacterResource($character),
        ]);
    }

    /**
     * Updates the specified resource.
     *
     * @param Player $player
     * @param Character $character
     * @param CharacterUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(Player $player, Character $character, CharacterUpdateRequest $request)
    {
        $character->update($request->validated());
        return back()->with('success', 'Character was successfully updated.');
    }

}
