<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\PanelLogResource;
use App\Http\Resources\PlayerIndexResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\WarningResource;
use App\Player;
use App\Warning;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
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
        $isOrdered = false;

        $query = Player::query();

        // Filtering by name.
        if ($name = $request->input('name')) {
            if (Str::startsWith($name, '=')) {
                $name = Str::substr($name, 1);
                $query->where('player_name', $name);
            } else {
                $query->where('player_name', 'like', "%{$name}%");
            }
        }

        // Filtering by steam_identifier.
        if ($steam = $request->input('steam')) {
            $query->where('steam_identifier', $steam);
        }

        // Filtering by discord.
        if ($discord = $request->input('discord')) {
            $query->where('identifiers', 'LIKE', '%discord:' . $discord . '%');
        }

        // Filtering isBanned.
        if ($banned = $request->input('banned')) {
            if ($banned === 'yes' || $banned === 'no') {
                $ids = Ban::getAllBans(true);

                if ($banned === 'yes') {
                    $query->whereIn('steam_identifier', $ids)->orderByRaw("FIELD(steam_identifier, '" . implode("','", $ids) . "') DESC");
                    $isOrdered = true;
                } else if ($banned === 'no') {
                    $query->whereNotIn('steam_identifier', $ids);
                }
            }
        }

        if (!$isOrdered) {
            $playerList = Player::getAllOnlinePlayers(true) ?? [];
            $players = array_keys($playerList);
            usort($players, function ($a, $b) use ($playerList) {
                return $playerList[$a]['id'] <=> $playerList[$b]['id'];
            });
            $players = array_map(function ($player) {
                return DB::connection()->getPdo()->quote($player);
            }, $players);

            if (!empty($players)) {
                $query->orderByRaw('FIELD(steam_identifier, ' . implode(', ', $players) . ') DESC, last_connection DESC');
            }
        }

        $query->select([
            'steam_identifier', 'player_name', 'playtime', 'identifiers',
        ]);
        $query->selectSub('SELECT COUNT(`id`) FROM `warnings` WHERE `player_id` = `user_id` AND `warning_type` = \'' . Warning::TypeWarning . '\'', 'warning_count');

        $page = Paginator::resolveCurrentPage('page');
        $query->limit(15)->offset(($page - 1) * 15);

        $players = $query->get();

        $end = round(microtime(true) * 1000);

        $identifiers = array_values(array_map(function ($player) {
            return $player['steam_identifier'];
        }, $players->toArray()));

        return Inertia::render('Players/Index', [
            'players' => PlayerIndexResource::collection($players),
            'banMap'  => Ban::getAllBans(false, $identifiers),
            'filters' => [
                'name'    => $request->input('name'),
                'steam'   => $request->input('steam'),
                'discord' => $request->input('discord'),
                'banned'  => $request->input('banned') ?: 'all',
            ],
            'links'   => $this->getPageUrls($page),
            'time'    => $end - $start,
            'page'    => $page,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Player $player
     * @return Response
     */
    public function show(Request $request, Player $player): Response
    {
        return Inertia::render('Players/Show', [
            'player'     => new PlayerResource($player),
            'characters' => CharacterResource::collection($player->characters),
            'warnings'   => WarningResource::collection($player->warnings()->oldest()->get()),
            'panelLogs'  => PanelLogResource::collection($player->panelLogs()->orderByDesc('timestamp')->limit(10)->get()),
            'discord'    => $player->getDiscordInfo(),
            'kickReason' => trim($request->query('kick')) ?? '',
        ]);
    }

}
