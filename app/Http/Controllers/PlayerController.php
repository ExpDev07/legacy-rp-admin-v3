<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacterResource;
use App\Http\Resources\PlayerIndexResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\WarningResource;
use App\Player;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlayerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $query = Player::query()->orderByDesc('playtime');

        // Querying.
        if ($q = $request->input('query')) {
            $query
                ->where('player_name', 'like', "%{$q}%")
                ->orWhere('steam_identifier', 'like', "%{$q}%")
                ->orWhere('identifiers', 'like', "%{$q}%");
        }

        $query->leftJoin('user_bans', 'steam_identifier', '=', 'identifier');
        $query->select([
            'steam_identifier', 'player_name', 'playtime', 'ban_hash'
        ]);
        $query->selectSub('SELECT COUNT(id) FROM warnings WHERE player_id=user_id', 'warning_count');

        return Inertia::render('Players/Index', [
            'players' => PlayerIndexResource::collection($query->paginate(10, [
                'user_id'
            ])->appends($request->query())),
            'filters' => $request->all('query'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Player $player
     * @return Response
     */
    public function show(Player $player): Response
    {
        return Inertia::render('Players/Show', [
            'player' => new PlayerResource($player),
            'characters' => CharacterResource::collection($player->characters),
            'warnings' => WarningResource::collection($player->warnings()->oldest()->get()),
        ]);
    }

}
