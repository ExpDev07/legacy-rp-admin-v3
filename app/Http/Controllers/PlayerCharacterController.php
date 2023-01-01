<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\CacheHelper;
use App\Helpers\OPFWHelper;
use App\Http\Requests\CharacterUpdateRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\CharacterIndexResource;
use App\Http\Resources\PlayerResource;
use App\Motel;
use App\PanelLog;
use App\Player;
use App\Property;
use App\Server;
use App\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PlayerCharacterController extends Controller
{
    const ValidTattooZones = [
        'all', 'head', 'left_arm', 'right_arm', 'torso', 'left_leg', 'right_leg',
    ];

    const Licenses = [
        "heli", "fw", "cfi", "hw", "hwh", "perf", "management", "military", "utility", "commercial", "special", "hunting", "fishing", "weapon"
    ];

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $start = round(microtime(true) * 1000);

		$characters = [];

		if (!$request->query('empty')) {
			$query = Character::query()->orderBy('first_name');

			// Filtering by cid.
			if ($cid = $request->input('character_id')) {
				$query->where('character_id', $cid);
			}

			// Filtering by name.
			if ($name = $request->input('name')) {
				if (Str::startsWith($name, '=')) {
					$name = Str::substr($name, 1);
					$query->where(DB::raw('CONCAT(first_name, \' \', last_name)'), $name);
				} else {
					$query->where(DB::raw('CONCAT(first_name, \' \', last_name)'), 'like', "%{$name}%");
				}
			}

			// Filtering by Vehicle Plate.
			if ($plate = $request->input('vehicle_plate')) {
				$query->whereHas('vehicles', function ($subQuery) use ($plate) {
					if (Str::startsWith($plate, '=')) {
						$plate = Str::substr($plate, 1);
						$subQuery->where('plate', $plate);
					} else {
						$subQuery->where('plate', 'like', "%{$plate}%");
					}
				});
			}

			// Filtering by Phone Number.
			if ($phone = $request->input('phone')) {
				if (Str::startsWith($phone, '=')) {
					$phone = Str::substr($phone, 1);
					$query->where('phone_number', $phone);
				} else {
					$query->where('phone_number', 'like', "%{$phone}%");
				}
			}

			// Filtering by DoB.
			if ($dob = $request->input('dob')) {
				if (Str::startsWith($dob, '=')) {
					$dob = Str::substr($dob, 1);
					$query->where('date_of_birth', $dob);
				} else {
					$query->where('date_of_birth', 'like', "%{$dob}%");
				}
			}

			// Filtering by Job.
			if ($job = $request->input('job')) {
				if (Str::startsWith($phone, '=')) {
					$job = Str::substr($job, 1);
					$query->where(DB::raw('CONCAT(job_name, \' \', department_name, \' \', position_name)'), $job);
				} else {
					$query->where(DB::raw('CONCAT(job_name, \' \', department_name, \' \', position_name)'), 'like', "%{$job}%");
				}
			}

			// Filtering isDeleted.
			if ($deleted = $request->input('deleted')) {
				if ($deleted === 'yes' || $deleted === 'no') {
					if ($deleted === 'yes') {
						$query->where('character_deleted', '=', '1');
					} else if ($deleted === 'no') {
						$query->where('character_deleted', '=', '0');
					}
				}
			}

			$query->select([
				'character_id', 'license_identifier', 'first_name', 'last_name', 'gender', 'job_name',
				'department_name', 'position_name', 'phone_number', 'date_of_birth'
			]);

			$characters = $query->paginate(15, [
				'id',
			])->appends($request->query());
		}

		$characters = CharacterIndexResource::collection($characters);

		$end = round(microtime(true) * 1000);

        return Inertia::render('Characters/Index', [
            'characters' => $characters,
            'filters'    => [
                'character_id'  => $request->input('character_id'),
                'name'          => $request->input('name'),
                'vehicle_plate' => $request->input('vehicle_plate'),
                'phone'         => $request->input('phone'),
                'dob'           => $request->input('dob'),
                'job'           => $request->input('job'),
                'deleted'       => $request->input('deleted') ?: 'all',
            ],
            'time'       => $end - $start,
            'playerMap'  => Player::fetchLicensePlayerNameMap($characters->toArray($request), 'licenseIdentifier'),
        ]);
    }

    /**
     * Display the specified resource for editing.
     *
     * @param Request $request
     * @param Player $player
     * @param Character $character
     * @return Response
     */
    public function edit(Request $request, Player $player, Character $character): Response
    {
        $resetCoords = json_decode(file_get_contents(__DIR__ . '/../../../helpers/coords_reset.json'), true);
        $motels = Motel::query()->where('cid', $character->character_id)->get()->sortBy(['motel', 'room_id']);
        $motelMap = json_decode(file_get_contents(__DIR__ . '/../../../helpers/motels.json'), true);

        $horns = Vehicle::getHornMap(false);

        $jobs = OPFWHelper::getJobsJSON(Server::getFirstServer() ?? '');

        return Inertia::render('Players/Characters/Edit', [
            'player'       => new PlayerResource($player),
            'character'    => new CharacterResource($character),
            'motels'       => $motels->toArray(),
            'motelMap'     => $motelMap,
            'horns'        => $horns,
            'vehicleMap'   => CacheHelper::getVehicleMap() ?? ['empty' => 'map'],
            'jobs'         => $jobs ? $jobs['jobs'] : [],
            'resetCoords'  => $resetCoords ? array_keys($resetCoords) : [],
            'vehicleValue' => Vehicle::getTotalVehicleValue($character->character_id),
            'returnTo'     => $_GET['returnTo'] ?? $player->license_identifier,
        ]);
    }

    public function backstories(Request $request): Response
    {
        return Inertia::render('Players/Characters/Backstories');
    }

    public function backstoriesApi(Request $request): \Illuminate\Http\Response
    {
        $character = Character::query()->orderByRaw('RAND()')->limit(1)->get()->first();

        if ($character) {
            return $this->json(true, (new CharacterResource($character))->toArray($request));
        }

        return $this->json(false, null, 'Failed to get character');
    }

    /**
     * Find a character by their cid
     *
     * @param Request $request
     * @param int $cid
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function find(Request $request, int $cid)
    {
        $character = Character::query()->select(['license_identifier', 'character_id'])->where('character_id', '=', $cid)->get()->first();

        if (!$character) {
            abort(404);
        }

        return redirect('/players/' . $character->license_identifier . '/characters/' . $character->character_id . '/edit');
    }

    /**
     * Updates the specified resource.
     *
     * @param Player $player
     * @param Character $character
     * @param CharacterUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(Player $player, Character $character, CharacterUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['date_of_birth'])) {
            $time = strtotime($data['date_of_birth']);
            if (!$time) {
                return back()->with('error', 'Invalid date of birth');
            }

            $data['date_of_birth'] = date('Y-m-d', $time);
        }

        if (!$data['job_name'] || $data['job_name'] === 'Unemployed') {
            $data['job_name'] = "Unemployed";
            $data['department_name'] = null;
            $data['position_name'] = null;
        }

        $changed = [];
        $old = $character->toArray();
        foreach ($data as $k => $v) {
            $c = isset($old[$k]) ? $old[$k] : null;
            if ($v !== $c) {
                $changed[] = $k;
            }
        }

        $character->update($data);

        $info = '';
        if ($request->query('jobUpdate')) {
            $info = 'In-Game Job refresh failed, user has to softnap.';
            $refresh = OPFWHelper::updateJob($player, $character->character_id);
            if ($refresh->status) {
                $info = 'In-Game Job refresh was successful too.';
            }
        }

        $user = $request->user();
        PanelLog::logCharacterEdit($user->player->license_identifier, $player->license_identifier, $character->character_id, $changed);

        return back()->with('success', 'Character was successfully updated. ' . $info);
    }

    /**
     * Deletes the specified resource.
     *
     * @param Player $player
     * @param Character $character
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request, Player $player, Character $character): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can delete characters.');
        }

        if ($character->character_deleted) {
            return back()->with('error', 'Character is already deleted.');
        }

        if (DB::statement('UPDATE `characters` SET `character_deleted` = 1, `character_deletion_timestamp`=' . time() . ' WHERE `character_id` = ' . $character->character_id)) {
            return back()->with('success', 'Character was successfully deleted.');
        }

        return back()->with('error', 'Failed to delete character.');
    }

    /**
     * Removes a characters tattoos
     *
     * @param Player $player
     * @param Character $character
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeTattoos(Player $player, Character $character, Request $request): RedirectResponse
    {
        $zone = $request->get('zone');
        $json = json_decode($character->tattoos_data, true);
        $map = json_decode(file_get_contents(__DIR__ . '/../../../helpers/tattoo-map.json'), true);

        if (!$map || !is_array($map)) {
            return back()->with('error', 'Failed to load zone map');
        }

        if (!$zone || !in_array($zone, self::ValidTattooZones)) {
            return back()->with('error', 'Invalid or no zone provided');
        }

        if ($zone === 'all') {
            $json = [];
        } else if (is_array($json)) {
            $result = [];
            foreach ($json as $tattoo) {
                if (!isset($tattoo['overlay'])) {
                    continue;
                }

                $key = strtolower($tattoo['overlay']);
                $z = isset($map[$key]) ? $map[$key]['zone'] : null;

                if (!$z || $z !== $zone) {
                    $result[] = $tattoo;
                }
            }

            $json = $result;
        } else {
            $json = [];
        }

        $character->update([
            'tattoos_data' => json_encode($json),
        ]);

        $user = $request->user();
        PanelLog::logTattooRemoval($user->player->license_identifier, $player->license_identifier, $character->character_id, $zone);

        $info = 'In-Game Tattoo refresh failed, user has to softnap.';
        $refresh = OPFWHelper::updateTattoos($player, $character->character_id);
        if ($refresh->status) {
            $info = 'In-Game tattoo refresh was successful too.';
        }

        return back()->with('success', 'Tattoos were removed successfully. ' . $info);
    }

    /**
     * Resets a characters spawn-point
     *
     * @param Player $player
     * @param Character $character
     * @param Request $request
     * @return RedirectResponse
     */
    public function resetSpawn(Player $player, Character $character, Request $request): RedirectResponse
    {
        $spawn = $request->get('spawn');
        $resetCoords = json_decode(file_get_contents(__DIR__ . '/../../../helpers/coords_reset.json'), true);

        if (!$resetCoords || !is_array($resetCoords)) {
            return back()->with('error', 'Failed to load spawn points');
        }

        if (!$spawn || (!isset($resetCoords[$spawn]) && $spawn !== "staff")) {
            return back()->with('error', 'Invalid or no spawn provided');
        }

        $coords = $spawn === "staff" ? '{"w":262.6,"x":-77.6,"y":-817.2,"z":321.285}' : json_encode($resetCoords[$spawn]);

        $character->update([
            'coords' => $coords,
        ]);

        $user = $request->user();
        PanelLog::logSpawnReset($user->player->license_identifier, $player->license_identifier, $character->character_id, $spawn);

        return back()->with('success', 'Spawn was reset successfully.');
    }

    /**
     * Edits a characters balance
     *
     * @param Player $player
     * @param Character $character
     * @param Request $request
     * @return RedirectResponse
     */
    public function editBalance(Player $player, Character $character, Request $request): RedirectResponse
    {
        $cash = intval($request->post("cash"));
        $bank = intval($request->post("bank"));
        $stocks = intval($request->post("stocks"));

        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can edit a characters balance.');
        }

        $character->update([
            'cash'           => $cash,
            'bank'           => $bank,
            'stocks_balance' => $stocks,
        ]);

        return back()->with('success', 'Balance has been updated successfully.');
    }

    /**
     * Deletes the specified vehicle.
     *
     * @param Request $request
     * @param Vehicle $vehicle
     * @return RedirectResponse
     */
    public function deleteVehicle(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can delete vehicles.');
        }

        $vehicle->update([
            'vehicle_deleted' => '1',
        ]);

        return back()->with('success', 'Vehicle was successfully deleted.');
    }

    /**
     * Adds the specified vehicle.
     *
     * @param Request $request
     * @param Player $player
     * @param Character $character
     * @return RedirectResponse
     */
    public function addVehicle(Request $request, Player $player, Character $character): RedirectResponse
    {
        $model = $request->post('model');

        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can add vehicles.');
        }

        $genPlate = function () {
            $a_z = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

            // For example: 28ULD493.
            return Str::upper(
                rand(0, 9) .
                rand(0, 9) .
                $a_z[rand(0, 25)] .
                $a_z[rand(0, 25)] .
                $a_z[rand(0, 25)] .
                rand(0, 9) .
                rand(0, 9) .
                rand(0, 9)
            );
        };

        $map = CacheHelper::getVehicleMap();
        if (!in_array($model, $map)) {
            return back()->with('error', 'Unknown model name "' . $model . '".');
        }

        $plate = $genPlate();
        $tries = 0;
        while ($tries < 100) {
            $tries++;

            $exists = Vehicle::query()->where('plate', '=', $plate)->count(['vehicle_id']) > 0;
            if (!$exists) {
                break;
            }

            $plate = $genPlate();
        }

        DB::table('character_vehicles')->insert([
            [
                'owner_cid'                => $character->character_id,
                'model_name'               => $model,
                'plate'                    => $plate,
                'mileage'                  => 0,
                'modifications'            => null,
                'data'                     => null,
                'garage_identifier'        => '*',
                'garage_state'             => 1,
                'garage_impound'           => 0,
                'deprecated_damage'        => null,
                'deprecated_modifications' => null,
                'deprecated_fuel'          => 100,
                'deprecated_supporter'     => 0,
                'vehicle_deleted'          => 0,
            ],
        ]);

        return back()->with('success', 'Vehicle was successfully added (Model: ' . $model . ', Plate: ' . $plate . ').');
    }

    /**
     * Adds the specified license.
     *
     * @param Request $request
     * @param Player $player
     * @param Character $character
     * @return RedirectResponse
     */
    public function addLicense(Request $request, Player $player, Character $character): RedirectResponse
    {
        $license = $request->post('license');

        if (!in_array($license, self::Licenses) && $license !== 'remove') {
            return back()->with('error', 'Invalid license "' . $license . '".');
        }

        $json = json_decode($character->character_data, true) ?? [];
        if (!isset($json['licenses'])) {
            $json['licenses'] = [];
        }

        if (in_array($license, $json['licenses'])) {
            return back()->with('error', 'Character already has license "' . $license . '".');
        }

        if ($license !== 'remove') {
            $json['licenses'][] = $license;
            $json['licenses'] = array_values(array_unique($json['licenses']));
        } else if (empty($json['licenses'])) {
            return back()->with('error', 'Character already has no licenses.');
        } else {
            $json['licenses'] = [];
        }

        $character->update([
            'character_data' => json_encode($json),
        ]);

        $user = $request->user();

        if ($license === 'remove') {
            PanelLog::logLicenseRemove($user->player->license_identifier, $player->license_identifier, $character->character_id);
            return back()->with('success', 'All Licenses were successfully removed.');
        }

        PanelLog::logLicenseAdd($user->player->license_identifier, $player->license_identifier, $character->character_id, $license);
        return back()->with('success', 'License was successfully added (License: ' . $license . ').');
    }

    /**
     * Resets the specified vehicles garage.
     *
     * @param Request $request
     * @param Vehicle $vehicle
     * @param bool $fullReset
     * @return \Illuminate\Http\Response
     */
    public function resetGarage(Request $request, Vehicle $vehicle, bool $fullReset): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
			return back()->with('error', 'Only super admins can reset vehicles garages.');
        }

		$data = [
			'last_garage_identifier' => null
		];

		if ($fullReset) {
			$data['garage_identifier'] = '*';
			$data['garage_state'] = 1;
			$data['garage_impound'] = 0;
		}

        $vehicle->update($data);

        return back()->with('success', 'Vehicle garage was successfully reset.');
    }

    /**
     * Returns basic character info for the map
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getCharacters(Request $request): \Illuminate\Http\Response
    {
        $ids = $request->post('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return (new \Illuminate\Http\Response([
                'status' => false,
            ], 200))->header('Content-Type', 'application/json');
        }

        $characters = Character::query()->whereIn('character_id', $ids)->select([
            'character_id', 'gender',
        ])->get()->toArray();

        return (new \Illuminate\Http\Response([
            'status' => true,
            'data'   => $characters,
        ], 200))->header('Content-Type', 'application/json');
    }

    public function export(Character $character, Request $request): \Illuminate\Http\Response
    {
        $export = [
            '**' . $character->first_name . ' ' . $character->last_name . '**',
            'DOB: - ' . $character->date_of_birth,
            'CID: - ' . $character->character_id,
            'Phone Number: - ' . $character->phone_number,
            'Gender: - ' . (intval($character->gender) === 1 ? 'Female' : 'Male'),
        ];

        $player = $character->player()->first();

        $discords = [];
        foreach ($player->getIdentifiers() as $identifier) {
            if (Str::startsWith($identifier, 'discord:')) {
                $discords[] = '<@' . str_replace('discord:', '', $identifier) . '>';
            }
        }
        $export[] = 'Email(s): - ' . ($discords ? implode(', ', $discords) : 'N/A');
        $export[] = '';

        // Export Vehicles
        $export[] = '**Vehicles**';

        $vehicles = $character->vehicles()->get();
        foreach ($vehicles as $vehicle) {
            $export[] = $vehicle->model_name . ' - ' . $vehicle->plate . ' - ' . $vehicle->garage();
        }

        if (empty($vehicles)) {
            $export[] = 'N/A';
        }

        $export[] = '';

        // Export Properties
        $export[] = '**Houses**';

        $properties = $character->properties()->get();
        foreach ($properties as $property) {
            $export[] = $property->property_address . ' - ' . $property->companyName();
        }

        if (empty($properties)) {
            $export[] = 'N/A';
        }

        return self::text(200, implode(PHP_EOL, $export));
    }

}
