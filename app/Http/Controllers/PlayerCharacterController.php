<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Requests\CharacterUpdateRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\CharacterIndexResource;
use App\Http\Resources\PlayerResource;
use App\Player;
use App\Property;
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

        // Filtering by Phone Number.
        if ($phone = $request->input('phone')) {
            $query->where('phone_number', 'like', "%{$phone}%");
        }

        // Filtering by Job.
        if ($job = $request->input('job')) {
            $query->where(DB::raw('CONCAT(job_name, \' \', department_name, \' \', position_name)'), 'like', "%{$job}%");
        }

        $query->leftJoin('users', 'characters.steam_identifier', '=', 'users.steam_identifier');
        $query->select([
            'character_id', 'characters.steam_identifier', 'first_name', 'last_name', 'gender', 'job_name',
            'department_name', 'position_name', 'player_name', 'phone_number'
        ]);

        return Inertia::render('Characters/Index', [
            'characters' => CharacterIndexResource::collection($query->paginate(15, [
                'id'
            ])->appends($request->query())),
            'filters' => $request->all(
                'cid',
                'name',
                'vehicle_plate',
                'phone',
                'job'
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
        var_dump($request->validated());

        $character->update($request->validated());
        return back()->with('success', 'Character was successfully updated.');
    }

}
