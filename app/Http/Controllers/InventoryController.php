<?php

namespace App\Http\Controllers;

use App\Character;
use App\Http\Resources\InventoryLogResource;
use App\Inventory;
use App\Log;
use App\Player;
use App\Vehicle;
use Illuminate\Http\Request;
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
        $query = Log::query()->orderByDesc('timestamp');

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

        $query->orWhereRaw('details REGEXP \'' . implode('|', $inventories) . '\'');

        $query->select(['id', 'identifier', 'action', 'details', 'metadata', 'timestamp']);

        $logs = InventoryLogResource::collection($query->paginate(15, [
            'id',
        ])->appends($request->query()));

        // This is a small hack but it speeds everything up by a lot
        $identifiers = [];
        foreach($logs->toArray($request) as $log) {
            if (!in_array($log['steamIdentifier'], $identifiers)) {
                $identifiers[] = $log['steamIdentifier'];
            }
        }

        $players = Player::query()->whereIn('steam_identifier', $identifiers)->get();
        $playerMap = [];
        foreach($players as $player) {
            $playerMap[$player->steam_identifier] = $player->player_name;
        }

        return Inertia::render('Inventories/Player', [
            'logs' => $logs,
            'playerMap' => $playerMap
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
