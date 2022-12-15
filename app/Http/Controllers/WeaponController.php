<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\OPFWHelper;
use App\Log;
use App\Player;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WeaponController extends Controller
{
    public function weaponDamage(Request $request, string $weapon): Response
    {
		$weapon = strtolower($weapon);
		$weaponHash = hexdec(hash("joaat", $weapon));

		$data = DB::table("weapon_damage_events")
			->selectRaw("COUNT(steam_identifier) as count, weapon_damage")
			->where("weapon_type", $weaponHash)
			->where("hit_players", "!=", "[]")
			->groupBy("weapon_damage")
			->orderByDesc("weapon_damage")
			->get()->toArray();

		$average = [
			'labels' => [],
            'data'   => [],
		];

		if (!empty($data)) {
			$maxDamage = min($data[0]->weapon_damage, 300);

			$damages = [];

			for ($damage = $maxDamage; $damage >= 0; $damage--) {
				$damages[$damage] = 0;
			}

			foreach ($data as $row) {
				$damages[min($row->weapon_damage, 300)] = $row->count;
			}

			foreach ($damages as $damage => $count) {
				$average['labels'][] = $damage;
				$average['data'][] = $count;
			}

			$average['labels'] = array_reverse($average['labels']);
			$average['data'] = array_reverse($average['data']);
		}

        return Inertia::render('Statistics/WeaponDamage', [
			'weapon' => $weapon,
			'weaponHash' => $weaponHash,
            'average' => $average
        ]);
    }
}
