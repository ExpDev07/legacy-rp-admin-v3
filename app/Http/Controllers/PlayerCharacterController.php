<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\OPFWHelper;
use App\Http\Requests\CharacterUpdateRequest;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\CharacterIndexResource;
use App\Http\Resources\PlayerResource;
use App\Motel;
use App\PanelLog;
use App\Player;
use App\Property;
use App\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PlayerCharacterController extends Controller
{
    const ValidTattooZones = [
        'all', 'head', 'left_arm', 'right_arm', 'torso', 'left_leg', 'right_leg',
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
            'character_id', 'steam_identifier', 'first_name', 'last_name', 'gender', 'job_name',
            'department_name', 'position_name', 'phone_number',
        ]);

        $characters = CharacterIndexResource::collection($query->paginate(15, [
            'id',
        ])->appends($request->query()));

        $end = round(microtime(true) * 1000);

        return Inertia::render('Characters/Index', [
            'characters' => $characters,
            'filters'    => [
                'character_id'  => $request->input('character_id'),
                'name'          => $request->input('name'),
                'vehicle_plate' => $request->input('vehicle_plate'),
                'phone'         => $request->input('phone'),
                'job'           => $request->input('job'),
                'deleted'       => $request->input('deleted') ?: 'all',
            ],
            'time'       => $end - $start,
            'playerMap'  => Player::fetchSteamPlayerNameMap($characters->toArray($request), 'steamIdentifier'),
        ]);
    }

    /**
     * Display the specified resource for editing.
     *
     * @param Player $player
     * @param Character $character
     * @return Response
     */
    public function edit(Player $player, Character $character): Response
    {
        $resetCoords = json_decode(file_get_contents(__DIR__ . '/../../../helpers/coords_reset.json'), true);
        $motels = Motel::query()->where('cid', $character->character_id)->get()->sortBy(['motel', 'room_id']);

        return Inertia::render('Players/Characters/Edit', [
            'player'      => new PlayerResource($player),
            'character'   => new CharacterResource($character),
            'motels'      => $motels->toArray(),
            'resetCoords' => $resetCoords ? array_keys($resetCoords) : [],
        ]);
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
        PanelLog::logCharacterEdit($user->player->steam_identifier, $player->steam_identifier, $character->character_id, $changed);

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
        PanelLog::logTattooRemoval($user->player->steam_identifier, $player->steam_identifier, $character->character_id, $zone);

        $info = 'In-Game Tattoo refresh failed, user has to softnap.';
        $refresh = OPFWHelper::updateTattoos($player, $character->character_id);
        if ($refresh->status) {
            $info = 'In-Game tattoo refresh was successful too.';
        }

        return back()->with('success', 'Tattoos were removed successfully. ' . $info);
    }

    /**
     * Resets a characters spawnpoint
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

        if (!$spawn || !isset($resetCoords[$spawn])) {
            return back()->with('error', 'Invalid or no spawn provided');
        }

        $character->update([
            'coords' => json_encode($resetCoords[$spawn]),
        ]);

        $user = $request->user();
        PanelLog::logSpawnReset($user->player->steam_identifier, $player->steam_identifier, $character->character_id, $spawn);

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

        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can edit a characters balance.');
        }

        $character->update([
            'cash' => $cash,
            'bank' => $bank,
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
     * Edits the specified vehicle.
     *
     * @param Request $request
     * @param Vehicle $vehicle
     * @return RedirectResponse
     */
    public function editVehicle(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can edit vehicles.');
        }

        $owner = $request->post('owner');

        $character = Character::query()->where('character_id', '=', $owner)->first(['character_id', 'steam_identifier']);
        if (!$character) {
            return back()->with('error', 'Invalid character id.');
        }

        $vehicle->update([
            'owner_cid' => $character->character_id,
        ]);

        return redirect('/players/' . $character->steam_identifier . '/characters/' . $character->character_id . '/edit')->with('success', 'Vehicle was successfully edited.');
    }

}
