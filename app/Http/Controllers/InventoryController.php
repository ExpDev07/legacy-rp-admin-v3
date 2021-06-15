<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Resources\InventoryLogResource;
use App\Inventory;
use App\Log;
use App\Player;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    /**
     * Display a inventory logs related to a player.
     *
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function player(Player $player, Request $request): Response
    {
        $start = round(microtime(true) * 1000);

        $query = Log::query()->orderByDesc('timestamp');

        $query->fromSub('SELECT * FROM user_logs WHERE action=\'Item Moved\'', 'logs');

        $inventories = [];

        $characters = $player->characters()->get();
        foreach ($characters as $character) {
            $query->orWhere('details', 'like', '%character-' . $character->character_id . ':%');

            $vehicles = $character->vehicles()->get();
            foreach ($vehicles as $vehicle) {
                $inventories[] = 'trunk-([0-9]{1,3}-)?' . $vehicle->vehicle_id . ':';
                $inventories[] = 'glovebox-([0-9]{1,3}-)?' . $vehicle->vehicle_id . ':';

                $inventories[] = 'trunk-([0-9]{1,3}-)?' . $vehicle->plate . ':';
                $inventories[] = 'glovebox-([0-9]{1,3}-)?' . $vehicle->plate . ':';
            }
        }

        $page = Paginator::resolveCurrentPage('page');
        $logs = InventoryLogResource::collection([]);
        if (!empty($inventories)) {
            $query->orWhere('identifier', $player->steam_identifier);
            $query->orWhereRaw('details REGEXP \'' . implode('|', $inventories) . '\'');

            $query->select(['id', 'identifier', 'action', 'details', 'metadata', 'timestamp']);

            $query->limit(15)->offset(($page - 1) * 15);

            $logs = InventoryLogResource::collection($query->get());
        }

        $url = $_SERVER['REQUEST_URI'];
        if (Str::contains($url, '?')) {
            $url .= '&';
        } else {
            $url .= '?';
        }
        $next = $url . 'page=' . ($page + 1);
        $prev = $url . 'page=' . ($page - 1);

        $end = round(microtime(true) * 1000);

        return Inertia::render('Inventories/Player', [
            'logs'      => $logs,
            'playerMap' => Player::fetchSteamPlayerNameMap($logs->toArray($request), 'steamIdentifier'),
            'links'     => [
                'next' => $next,
                'prev' => $prev,
            ],
            'time'      => $end - $start,
            'page'      => $page,
        ]);
    }

    /**
     * Display informations related to an inventory.
     *
     * @param string $inventory
     * @param Request $request
     * @return Response
     */
    public function show(string $inventory, Request $request): Response
    {
        return Inertia::render('Inventories/Show', [
            'inventory' => Inventory::parseDescriptor($inventory)->get(),
        ]);
    }

}
