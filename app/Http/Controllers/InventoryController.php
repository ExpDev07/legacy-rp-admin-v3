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
            $inventories[] = 'character-' . $character->character_id;

            $vehicles = $character->vehicles()->get();
            foreach ($vehicles as $vehicle) {
                $inventories[] = 'trunk-([0-9]+-)?' . $vehicle->vehicle_id;
                $inventories[] = 'glovebox-([0-9]+-)?' . $vehicle->vehicle_id;

                $inventories[] = 'trunk-([0-9]+-)?' . $vehicle->plate;
                $inventories[] = 'glovebox-([0-9]+-)?' . $vehicle->plate;;
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
            'type' => 'inventory',
        ]);
    }

    /**
     * Display Informations related to an inventory.
     *
     * @param string $inventory
     * @param Request $request
     * @return Response
     */
    public function show(string $inventory, Request $request): Response
    {
        $split = explode('-', $inventory);

        if (sizeof($split) < 2) {
            return Inertia::render('Inventories/Show', [
                'inventory' => null,
            ]);
        }

        $inventory = new Inventory($inventory);

        $id = explode(':', $split[sizeof($split) - 1])[0];

        $type = $split[0];
        $inventory->type = $type;
        switch ($type) {
            case 'ground':
                break;
            case 'character':
                $query = Character::query()->where('character_id', $id);
                $inventory->character = $query->first();
                break;
            case 'trunk':
            case 'glovebox':
                $query = Vehicle::query();
                if (is_numeric($id)) {
                    $query->where('vehicle_id', $id);
                } else {
                    $query->where('plate', $id);
                }
                $inventory->vehicle = $query->first();

                if ($inventory->vehicle) {
                    $inventory->character = $inventory->vehicle->character()->first();
                }
                break;
        }

        return Inertia::render('Inventories/Show', [
            'inventory' => $inventory,
        ]);
    }

}
