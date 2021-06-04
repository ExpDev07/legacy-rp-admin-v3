<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Requests\CharacterUpdateRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\ExtendedCharacterResource;
use App\Http\Resources\PlayerResource;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PlayerCharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        $query = Character::query()->orderBy('first_name');

        // Filtering by cid.
        if ($cid = $request->input('character_id')) {
            $query->where('character_id', $cid);
        }

        // Filtering by name.
        if ($name = $request->input('name')) {
            $query->where(DB::raw('CONCAT(first_name, \' \', last_name)'), 'like', "%{$name}%");
        }

        // Filtering by Vehicle Plate.
        if ($plate = $request->input('vehicle_plate')) {
            $query->whereHas('vehicles', function($subQuery) use ($plate) {
                $subQuery->where('plate', $plate);
            });
        }

        return Inertia::render('Characters/Index', [
            'characters' => ExtendedCharacterResource::collection($query->simplePaginate(15)->appends($request->query())),
            'filters' => $request->all(
                'cid',
                'name'
            ),
        ]);
    }

    /**
     * Display the specified resource for editing.
     *
     * @param Player $player
     * @param Character $character
     * @return Response
     */
    public function edit(Player $player, Character $character): Response
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
