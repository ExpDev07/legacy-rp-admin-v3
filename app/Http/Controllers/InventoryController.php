<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\CacheHelper;
use App\Http\Resources\InventoryLogResource;
use App\Inventory;
use App\Motel;
use App\Player;
use App\Property;
use App\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    /**
     * Display inventory logs related to a character.
     *
     * @param Character $character
     * @param Request $request
     * @return Response
     */
    public function character(Character $character, Request $request): Response
    {
        $inventories = [
            'character-' . $character->character_id,
            'locker-police-' . $character->character_id,
            'locker-mechanic-' . $character->character_id,
            'locker-ems-' . $character->character_id,
        ];

        return $this->searchInventories($request, $inventories);
    }

    /**
     * Display inventory logs related to a vehicle.
     *
     * @param Vehicle $vehicle
     * @param Request $request
     * @return Response
     */
    public function vehicle(Vehicle $vehicle, Request $request): Response
    {
        $inventories = [
            'trunk-%-' . $vehicle->plate,
            'trunk-%-' . $vehicle->vehicle_id,
            'glovebox-' . $vehicle->plate,
            'glovebox-' . $vehicle->vehicle_id,
        ];

        return $this->searchInventories($request, $inventories, true);
    }

    /**
     * Display inventory logs related to a property.
     *
     * @param Property $property
     * @param Request $request
     * @return Response
     */
    public function property(Property $property, Request $request): Response
    {
        $inventories = [
            'property-' . $property->property_id . '-%',
        ];

        return $this->searchInventories($request, $inventories, 'LIKE');
    }

    /**
     * Display inventory logs related to a motel.
     *
     * @param Motel $motel
     * @param Request $request
     * @return Response
     */
    public function motel(Motel $motel, Request $request): Response
    {
        $motelMap = json_decode(file_get_contents(__DIR__ . '/../../../helpers/motels.json'), true);

        $inventories = [
            'motel-' . $motelMap[$motel->motel] . '-' . $motel->room_id,
        ];

        return $this->searchInventories($request, $inventories, 'LIKE');
    }

    /**
     * Display inventory logs related to a raw inventory identifier.
     *
     * @param string $identifier
     * @param Request $request
     * @return Response
     */
    public function raw(string $identifier, Request $request): Response
    {
        $inventory = Inventory::parseDescriptor($identifier);

        $inventories = [
            $inventory->title
        ];

        return $this->searchInventories($request, $inventories, 'LIKE');
    }

    /**
     * @param Request $request
     * @param array $inventories
     * @param bool $likeSearch
     * @return Response
     */
    private function searchInventories(Request $request, array $inventories, bool $likeSearch = false): Response
    {
        $start = round(microtime(true) * 1000);

        $fromSql = "SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, ' to ', -1), ' from ', 1), ':', 1)";
        $toSql = "SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, ' to ', -1), ' from inventory ', -1), ':', 1)";

        if ($likeSearch) {
            $where = [];
            foreach ($inventories as $inventory) {
                $where[] = "(" . $fromSql . " LIKE '" . $inventory . "' OR " . $toSql . " LIKE '" . $inventory . "')";
            }
            $where = implode(' OR ', $where);
        } else {
            $where = "(" . $fromSql . " IN ('" . implode("', '", $inventories) . "') OR " . $toSql . " IN ('" . implode("', '", $inventories) . "'))";
        }

        $page = Paginator::resolveCurrentPage('page');

        $sql = "SELECT `identifier`, `details`, `timestamp` FROM `user_logs` WHERE `action`='Item Moved' AND (" . $where . ") ORDER BY `timestamp` DESC LIMIT 15 OFFSET " . (($page - 1) * 15);

        $logs = InventoryLogResource::collection(DB::select($sql));

        $end = round(microtime(true) * 1000);

        return Inertia::render('Inventories/Index', [
            'logs'      => $logs,
            'playerMap' => Player::fetchLicensePlayerNameMap($logs->toArray($request), 'licenseIdentifier'),
            'links'     => $this->getPageUrls($page),
            'time'      => $end - $start,
            'page'      => $page,
        ]);
    }

    /**
     * Creates a snapshot of an inventory at that exact moment in time
     *
     * @param string $inventory
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createSnapshot(string $inventory, Request $request): \Illuminate\Http\Response
    {
        $inventory = Inventory::parseDescriptor($inventory);
        $name = explode(':', $inventory->descriptor)[0];

        $contents = DB::table('inventories')
            ->where('inventory_name', '=', $name)
            ->select(['id', 'item_name', 'inventory_slot'])
            ->get()->toArray();

        $snapshot = [
            'hash'           => Str::uuid()->toString(),
            'inventory_name' => $name,
            'contents'       => $contents,
            'created'        => time(),
            'expires'        => time() + (2 * 24 * 60 * 60),
            'created_by'     => $request->user()->player->license_identifier,
        ];

        CacheHelper::write('inv_snap_' . $snapshot['hash'], $snapshot, 2 * CacheHelper::DAY);

        return (new \Illuminate\Http\Response(json_encode(['hash' => $snapshot['hash']]), 200))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Show a snapshot
     *
     * @param string $snapshot
     * @param Request $request
     * @return Response|null
     */
    public function showSnapshot(string $snapshot, Request $request): ?Response
    {
        $key = 'inv_snap_' . $snapshot;
        if (!CacheHelper::exists($key)) {
            abort(404, 'Snapshot not found.');
            return null;
        }

        $snapshot = CacheHelper::read($key);

        $superAdmin = $request->user()->player->is_super_admin;
        if (!$superAdmin) {
            $snapshot['contents'] = [];
        }

        $snapshot['created_by'] = Player::query()->where('license_identifier', '=', $snapshot['created_by'])->select([
            'player_name',
        ])->first()->toArray();

        $inventory = Inventory::parseDescriptor($snapshot['inventory_name'])->get();

        return Inertia::render('Inventories/Show', [
            'inventory' => $inventory,
            'contents'  => $snapshot['contents'],
            'snapshot'  => $snapshot,
        ]);
    }

    /**
     * Display information related to an inventory.
     *
     * @param string $inventory
     * @param Request $request
     * @return Response
     */
    public function show(string $inventory, Request $request): Response
    {
        $inventory = Inventory::parseDescriptor($inventory)->get();

        $superAdmin = $request->user()->player->is_super_admin;
        if ($superAdmin) {
            $contents = DB::table('inventories')
                ->where('inventory_name', '=', explode(':', $inventory->descriptor)[0])
                ->select(['id', 'item_name', 'inventory_slot'])
                ->get()->toArray();
        } else {
            $contents = [];
        }

        return Inertia::render('Inventories/Show', [
            'inventory' => $inventory,
            'contents'  => $contents,
        ]);
    }

    /**
     * Finds an inventory of a certain type.
     *
     * @param string $type
     * @param string $id
     * @param Request $request
     * @return RedirectResponse|void
     */
    public function find(string $type, string $id, Request $request)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        $inv = null;
        switch ($type) {
            case 'trunk':
                $inv = DB::table('inventories')
                    ->where('inventory_name', 'LIKE', 'trunk-%')
                    ->where('inventory_name', 'LIKE', '%-' . $id)
                    ->select(['inventory_name'])->first();
                break;
            case 'glovebox':
                $inv = DB::table('inventories')
                    ->where('inventory_name', 'LIKE', 'glovebox-%')
                    ->where('inventory_name', 'LIKE', '%-' . $id)
                    ->select(['inventory_name'])->first();
                break;
        }

        if ($inv) {
            $url = '/inventory/' . $inv->inventory_name . ':1';

            return redirect($url);
        } else {
            abort(404);
        }
    }

    /**
     * Clears a slot in a certain inventory
     *
     * @param string $inventory
     * @param int $slot
     * @param Request $request
     * @return RedirectResponse
     */
    public function clear(string $inventory, int $slot, Request $request): RedirectResponse
    {
        $inventory = Inventory::parseDescriptor($inventory)->get();

        $superAdmin = $request->user()->player->is_super_admin;
        if (!$superAdmin) {
            return back()->with('error', 'You are not a super admin');
        }

        $name = explode(':', $inventory->descriptor)[0];

        $content = DB::table('inventories')
            ->where('inventory_name', '=', $name)
            ->where('inventory_slot', '=', $slot)
            ->select(['id'])
            ->first();

        if (!$content) {
            return back()->with('error', 'Slot ' . $slot . ' in inventory ' . $name . ' is already empty');
        }

        DB::table('inventories')
            ->where('inventory_name', '=', $name)
            ->where('inventory_slot', '=', $slot)
            ->delete();

        return back()->with('success', 'Cleared slot ' . $slot . ' in inventory ' . $name);
    }

    /**
     * Search for inventories
     *
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $inventories = [];

        $cid = intval($request->input('inventory_cid')) ?? null;
        $plate = trim($request->input('inventory_plate_id')) ?? null;
        $evidenceId = intval($request->input('inventory_evidence_id')) ?? null;

        $character = null;
        $vehicle = null;
        switch ($request->input('inventory_type')) {
            case 'character':
            case 'motel':
            case 'property':
                if ($cid) {
                    $character = Character::query()->where('character_id', '=', $cid)->first();
                }
                break;
            case 'vehicle':
                if ($plate) {
                    if (is_numeric($plate)) {
                        $vehicle = Vehicle::query()->where('vehicle_id', '=', intval($plate))->first();
                    } else {
                        $vehicle = Vehicle::query()->where('plate', '=', $plate)->first();
                    }
                }
                break;
        }

        switch ($request->input('inventory_type')) {
            case 'character':
                if ($character) {
                    $name = $character->first_name . ' ' . $character->last_name;
                    $id = $character->character_id;

                    $inventories = [
                        ['name' => $name . ' Pockets', 'id' => 'character-' . $id],
                        ['name' => $name . ' PD Locker', 'id' => 'locker-police-' . $id],
                        ['name' => $name . ' EMS Locker', 'id' => 'locker-ems-' . $id],
                        ['name' => $name . ' Mechanic Locker', 'id' => 'locker-mechanic-' . $id],
                    ];
                }
                break;
            case 'vehicle':
                if ($vehicle) {
                    $trunk = DB::table('inventories')
                        ->where('inventory_name', 'LIKE', 'trunk-%')
                        ->where('inventory_name', 'LIKE', '%-' . $vehicle->vehicle_id)
                        ->select(['inventory_name'])->first();

                    if ($trunk) {
                        $inventories[] = [
                            'name' => 'Trunk (' . $vehicle->plate . ')',
                            'id'   => $trunk->inventory_name,
                        ];
                    }

                    $glove = DB::table('inventories')
                        ->where('inventory_name', 'LIKE', 'glovebox-%')
                        ->where('inventory_name', 'LIKE', '%-' . $vehicle->vehicle_id)
                        ->select(['inventory_name'])->first();

                    if ($glove) {
                        $inventories[] = [
                            'name' => 'Glove-box (' . $vehicle->plate . ')',
                            'id'   => $glove->inventory_name,
                        ];
                    }
                }
                break;
            case 'evidence':
                if ($evidenceId) {
                    $invs = DB::table('inventories')
                        ->where('inventory_name', 'LIKE', 'evidence-' . $evidenceId . '-%')
                        ->groupBy(['inventory_name'])
                        ->select(['inventory_name'])->get()->toArray();

                    foreach ($invs as $inv) {
                        $split = explode('-', $inv->inventory_name);
                        if (sizeof($split) !== 3) {
                            continue;
                        }

                        $inventories[] = ['name' => 'Evidence ' . $split[2], 'id' => $inv->inventory_name];
                    }
                }
                break;
            case 'motel':
                if ($character) {
                    $map = json_decode(file_get_contents(__DIR__ . '/../../../helpers/motels.json'), true);

                    if (is_array($map) && !empty($map)) {
                        $motels = Motel::query()
                            ->where('cid', '=', $character->character_id)
                            ->select(['motel', 'room_id'])
                            ->get()->toArray();

                        foreach ($motels as $motel) {
                            $id = isset($map[$motel['motel']]) ? $map[$motel['motel']] : null;

                            if ($id) {
                                $inventories[] = [
                                    'name' => $motel['motel'] . ' (Room ' . $motel['room_id'] . ')',
                                    'id'   => 'motel-' . $id . '-' . $motel['room_id'],
                                ];
                            }
                        }
                    }
                }
                break;
            case 'property':
                if ($character) {
                    $properties = Property::query()
                        ->where('property_renter_cid', '=', $character->character_id)
                        ->select([
                            'property_address',
                            'property_id',
                        ])->get()->toArray();

                    if (!empty($properties)) {
                        $query = DB::table('inventories');
                        $names = [];
                        foreach ($properties as $property) {
                            $names[$property['property_id']] = $property['property_address'];

                            $query->orWhere('inventory_name', 'LIKE', 'property-' . $property['property_id'] . '-%');
                        }

                        $inv = $query->groupBy(['inventory_name'])->select(['inventory_name'])->get()->toArray();
                        foreach ($inv as $inventory) {
                            $split = explode('-', $inventory->inventory_name);
                            if (sizeof($split) !== 3) {
                                continue;
                            }

                            $id = $split[1];
                            $container = $split[2];

                            $inventories[] = [
                                'name' => $names[$id . ''] . ' (Container ' . $container . ')',
                                'id'   => $inventory->inventory_name,
                            ];
                        }
                    }
                }
                break;
        }

        return Inertia::render('Inventories/Search', [
            'inventories' => $inventories,
            'filters'     => [
                'inventory_type'        => $request->input('inventory_type') ?? 'character',
                'inventory_cid'         => $request->input('inventory_cid'),
                'inventory_plate_id'    => $request->input('inventory_plate_id'),
                'inventory_evidence_id' => $request->input('inventory_evidence_id') ?? 1,
            ],
        ]);
    }

}
