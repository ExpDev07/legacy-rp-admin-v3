<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\PanelLogResource;
use App\Http\Resources\PlayerIndexResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\WarningResource;
use App\Player;
use App\Screenshot;
use App\Server;
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

        // Filtering by serer-id.
        if ($server = $request->input('server')) {
            $online = array_keys(array_filter(Player::getAllOnlinePlayers(true), function ($player) use ($server) {
                return $player['id'] === intval($server);
            }));

            $query->whereIn('steam_identifier', $online);
        }

        // Filtering isBanned.
        if ($banned = $request->input('banned')) {
            if (in_array($banned, ['yes', 'no', 'mine'])) {
                $ids = Ban::getAllBans(true);

                if ($banned === 'yes') {
                    $query->whereIn('steam_identifier', $ids)->orderByRaw("FIELD(steam_identifier, '" . implode("','", $ids) . "') DESC");
                    $isOrdered = true;
                } else if ($banned === 'no') {
                    $query->whereNotIn('steam_identifier', $ids);
                } else if ($banned === 'mine') {
                    $player = $request->user()->player;
                    $ids = Ban::getAllBannedIdentifiersByCreator($player->player_name, $player->steam_identifier);

                    $query->whereIn('steam_identifier', $ids)->orderByRaw("FIELD(steam_identifier, '" . implode("','", $ids) . "') DESC");
                    $isOrdered = true;
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

            $orderField = $request->input('order') ?? null;
            $orderDirection = $request->input('desc') ? 'DESC' : 'ASC';

            if (!$orderField || !in_array($orderField, ['last_connection', 'playtime', 'player_name'])) {
                $orderField = 'last_connection';
                $orderDirection = 'DESC';
            }

            if (!empty($players)) {
                $query->orderByRaw('FIELD(steam_identifier, ' . implode(', ', $players) . ') DESC, ' . $orderField . ' ' . $orderDirection);
            } else {
                $query->orderByDesc($orderField);
            }
        }

        $query->select([
            'steam_identifier', 'player_name', 'playtime', 'identifiers',
        ]);
        $query->selectSub('SELECT COUNT(`id`) FROM `warnings` WHERE `player_id` = `user_id` AND `warning_type` IN (\'' . Warning::TypeWarning . '\', \'' . Warning::TypeStrike . '\')', 'warning_count');

        $page = Paginator::resolveCurrentPage('page');
        $query->limit(15)->offset(($page - 1) * 15);

        $players = $query->get();

        $end = round(microtime(true) * 1000);

        $identifiers = array_values(array_map(function ($player) {
            return $player['steam_identifier'];
        }, $players->toArray()));

        return Inertia::render('Players/Index', [
            'players' => PlayerIndexResource::collection($players),
            'banMap'  => Ban::getAllBans(false, $identifiers, true),
            'filters' => [
                'name'    => $request->input('name'),
                'steam'   => $request->input('steam'),
                'discord' => $request->input('discord'),
                'server'  => $request->input('server'),
                'banned'  => $request->input('banned') ?: 'all',
            ],
            'links'   => $this->getPageUrls($page),
            'time'    => $end - $start,
            'page'    => $page,
        ]);
    }

    /**
     * Display a listing of all online new players.
     *
     * @param Request $request
     * @return Response
     */
    public function newPlayers(Request $request): Response
    {
        $query = Player::query();

        $playerList = Player::getAllOnlinePlayers(true) ?? [];
        $players = array_keys($playerList);

        $query->whereIn('steam_identifier', $players);
        $query->where('playtime', '<=', 60 * 60 * 24);

        $query->orderBy('playtime');

        $players = $query->get();

        return Inertia::render('Players/NewPlayers', [
            'players' => PlayerIndexResource::collection($players)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param string $player
     * @return Response|void
     */
    public function show(Request $request, string $player)
    {
        $resolved = Player::resolvePlayer($player, $request);

        if ($resolved) {
            if (is_array($resolved)) {
                return Inertia::render('Players/Show', $resolved);
            } else {
                $whitelisted = DB::table('user_whitelist')
                    ->select(['steam_identifier'])
                    ->where('steam_identifier', '=', $resolved->steam_identifier)
                    ->first();

                return Inertia::render('Players/Show', [
                    'player'      => new PlayerResource($resolved),
                    'characters'  => CharacterResource::collection($resolved->characters),
                    'warnings'    => WarningResource::collection($resolved->warnings()->oldest()->get()),
                    'panelLogs'   => PanelLogResource::collection($resolved->panelLogs()->orderByDesc('timestamp')->limit(10)->get()),
                    'discord'     => $resolved->getDiscordInfo(),
                    'kickReason'  => trim($request->query('kick')) ?? '',
                    'screenshots' => Screenshot::getAllScreenshotsForPlayer($resolved->steam_identifier),
                    'whitelisted' => !!$whitelisted,
                ]);
            }
        } else {
            abort(404);
        }
    }

}
