<?php

namespace App\Http\Controllers;

use App\Ban;
use App\BlacklistedIdentifier;
use App\Helpers\GeneralHelper;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\PanelLogResource;
use App\Http\Resources\PlayerIndexResource;
use App\Http\Resources\PlayerResource;
use App\Http\Controllers\PlayerRouteController;
use App\Player;
use App\Character;
use App\Screenshot;
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
     */
    public function index(Request $request)
    {
		$identifiers = [];
		$players = [];
		$page = 1;

		$start = round(microtime(true) * 1000);

		if (!$request->query('empty')) {
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

			// Filtering by identifier.
			if ($identifier = $request->input('identifier')) {
				$query->where('identifiers', 'like', "%{$identifier}%");
			}

			// Filtering by license_identifier.
			if ($license = $request->input('license')) {
				if (!Str::startsWith($license, 'license:')) {
					$license = 'license:' . $license;
				}

				if (strlen($license) !== 48) {
					$query->where('license_identifier', 'LIKE', '%' . $license);
				} else {
					$query->where('license_identifier', $license);
				}
			}

			// Filtering by discord.
			if ($discord = $request->input('discord')) {
				$query->where('identifiers', 'LIKE', '%discord:' . $discord . '%');
			}

			// Filtering by serer-id.
			if ($server = $request->input('server')) {
				$online = array_keys(array_filter(Player::getAllOnlinePlayers(true) ?? [], function ($player) use ($server) {
					return $player['id'] === intval($server);
				}));

				$query->whereIn('license_identifier', $online);
			}

			$query->orderBy("player_name");

			$query->select([
				'license_identifier', 'player_name', 'playtime', 'identifiers',
			]);
			$query->selectSub('SELECT COUNT(`id`) FROM `warnings` WHERE `player_id` = `user_id` AND `warning_type` IN (\'' . Warning::TypeWarning . '\', \'' . Warning::TypeStrike . '\')', 'warning_count');

			$page = Paginator::resolveCurrentPage('page');
			$query->limit(15)->offset(($page - 1) * 15);

			$players = $query->get();

			if ($players->count() === 1) {
				$player = $players->first();

				return redirect('/players/' . $player->license_identifier);
			}

			$identifiers = array_values(array_map(function ($player) {
				return $player['license_identifier'];
			}, $players->toArray()));
		}

		$end = round(microtime(true) * 1000);

        return Inertia::render('Players/Index', [
            'players' => PlayerIndexResource::collection($players),
            'banMap'  => Ban::getAllBans(false, $identifiers, true),
            'filters' => [
                'name'    => $request->input('name'),
                'license' => $request->input('license'),
                'discord' => $request->input('discord'),
                'server'  => $request->input('server'),
                'identifier' => $request->input('identifier'),
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

        $query->whereIn('license_identifier', $players);
        $query->where('playtime', '<=', 60 * 60 * 12);

        $query->orderBy('playtime');

        $players = $query->get();

        $characterIds = [];

        foreach ($players as $player) {
            $status = Player::getOnlineStatus($player->license_identifier, true);

            if ($status->character) {
                $characterIds[] = $status->character;
            }
        }

        $characters = !empty($characterIds) ? Character::query()->whereIn('character_id', $characterIds)->get() : [];

        $playerList = [];

        foreach ($players as $player) {
            $character = null;

            foreach ($characters as $char) {
                if ($char->license_identifier === $player->license_identifier) {
                    $character = $char;

                    break;
                }
            }

            $status = Player::getOnlineStatus($player->license_identifier, true);

            $playerList[] = [
                'serverId' => $status && $status->serverId ? $status->serverId : null,
                'character' => $character ? [
                    'name' => $character->first_name . ' ' . $character->last_name,
                    'backstory' => $character->backstory,
                    'danny' => GeneralHelper::isDefaultDanny(intval($character->ped_model_hash), $character->ped_model_data)
                ] : null,
                'playerName' => $player->player_name,
                'playTime' => $player->playtime,
                'licenseIdentifier' => $player->license_identifier,
            ];
        }

        return Inertia::render('Players/NewPlayers', [
            'players' => $playerList,
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
		if (Str::startsWith($player, 'steam:')) {
			$player = $player = Player::findPlayerBySteam($player);

			if (!$player) {
				return abort(404);
			}

			return redirect('/players/' . $player->license_identifier);
		}

        $resolved = Player::resolvePlayer($player, $request);

        if ($resolved) {
			$whitelisted = DB::table('user_whitelist')
				->select(['license_identifier'])
				->where('license_identifier', '=', $resolved->license_identifier)
				->first();

			$identifiers = $resolved instanceof Player ? $resolved->getIdentifiers() : null;

			$blacklisted = !empty($identifiers) ? BlacklistedIdentifier::query()
				->select(['identifier'])
				->whereIn('identifier', $identifiers)
				->first() : false;

			$isSenior = $this->isSeniorStaff($request);

			return Inertia::render('Players/Show', [
				'player'            => new PlayerResource($resolved),
				'characters'        => CharacterResource::collection($resolved->characters),
				'warnings'          => $resolved->fasterWarnings($isSenior),
				'kickReason'        => trim($request->query('kick')) ?? '',
				'whitelisted'       => !!$whitelisted,
				'blacklisted'       => !!$blacklisted,
				'tags'              => Player::resolveTags(),
				'allowRoleEdit'     => env('ALLOW_ROLE_EDITING', false) && $this->isSuperAdmin($request),
				'enablableCommands' => PlayerRouteController::EnablableCommands
			]);
        } else {
            abort(404);
        }
    }

}
