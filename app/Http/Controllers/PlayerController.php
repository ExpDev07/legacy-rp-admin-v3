<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacterResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\WarningResource;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PlayerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Player::query()->orderBy('player_name');

        // Querying.
        if ($name = $request->input('name')) {
            $query->where('player_name', 'like', "%{$name}%");
        }

        return Inertia::render('Players/Index', [
            'players' => PlayerResource::collection($query->simplePaginate(10)),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Player $player
     * @return \Inertia\Response
     */
    public function show(Player $player)
    {
        return Inertia::render('Players/Show', [
            'player' => new PlayerResource($player),
            'characters' => CharacterResource::collection($player->characters),
            'warnings' => WarningResource::collection($player->warnings()->oldest()->get()),
        ]);
    }

}
