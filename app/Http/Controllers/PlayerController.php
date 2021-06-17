<?php

namespace App\Http\Controllers;

use App\Http\Resources\CharacterResource;
use App\Http\Resources\PlayerIndexResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\WarningResource;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        $start = round(microtime(true) * 1000);

        $playerList = Player::getAllOnlinePlayers(true);
        $players = array_keys($playerList);
        usort($players, function($a, $b) use ($playerList) {
            return $playerList[$a]['id'] <=> $playerList[$b]['id'];
        });
        $players = array_map(function($player) {
            return DB::connection()->getPdo()->quote($player);
        }, $players);

        $query = Player::query()->orderByRaw('FIELD(steam_identifier, ' . implode(', ', $players) . ') DESC, playtime DESC');

        // Querying.
        if ($q = $request->input('query')) {
            if (Str::startsWith($q, 'identifier=')) {
                $q = str_replace('identifier=', '', $q);
                $query->where('steam_identifier', $q);
            } else if (Str::startsWith($q, 'name=')) {
                $q = str_replace('name=', '', $q);
                $query->where('player_name', $q);
            } else {
                $query
                    ->where('player_name', 'like', "%{$q}%")
                    ->orWhere('steam_identifier', 'like', "%{$q}%")
                    ->orWhere('identifiers', 'like', "%{$q}%");
            }
        }

        $query->select([
            'steam_identifier', 'player_name', 'playtime', 'identifiers',
        ]);
        $query->selectSub('SELECT COUNT(id) FROM warnings WHERE player_id=user_id', 'warning_count');

        $players = $query->paginate(10, [
            'user_id',
        ])->appends($request->query());

        $end = round(microtime(true) * 1000);

        return Inertia::render('Players/Index', [
            'players' => PlayerIndexResource::collection($players),
            'filters' => $request->all('query'),
            'time'    => $end - $start,
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
            'player'     => new PlayerResource($player),
            'characters' => CharacterResource::collection($player->characters),
            'warnings'   => WarningResource::collection($player->warnings()->oldest()->get()),
        ]);
    }

}
