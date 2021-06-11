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
            $inventories[] = 'character-' . $character->character_id . ':[0-9]{1,3}';

            $vehicles = $character->vehicles()->get();
            foreach ($vehicles as $vehicle) {
                $inventories[] = 'trunk-([0-9]{1,3}-)?' . $vehicle->vehicle_id . ':[0-9]{1,3}';
                $inventories[] = 'glovebox-([0-9]{1,3}-)?' . $vehicle->vehicle_id . ':[0-9]{1,3}';

                $inventories[] = 'trunk-([0-9]{1,3}-)?' . $vehicle->plate . ':[0-9]{1,3}';
                $inventories[] = 'glovebox-([0-9]{1,3}-)?' . $vehicle->plate . ':[0-9]{1,3}';
            }
        }

        foreach ($inventories as $inventory) {
            $query->orWhereRaw('details REGEXP \'' . $inventory . '\'');
        }

        $query->leftJoin('users', 'identifier', '=', 'steam_identifier');
        $query->select(['id', 'identifier', 'action', 'details', 'metadata', 'timestamp', 'player_name']);

        return Inertia::render('Inventories/Player', [
            'logs' => InventoryLogResource::collection($query->paginate(15, [
                'id',
            ])->appends($request->query())),
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
