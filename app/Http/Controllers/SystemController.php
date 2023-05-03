<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    const AntiCheatTypes = [
        "illegal_event",
        "illegal_server_event",
        "spawned_object",
        "distance_taze",
        "ped_spawn",
        "spectate",
        "runtime_texture",
        "ped_change",
        "illegal_weapon",
        "damage_modifier",
        "vehicle_modification",
        "thermal_night_vision",
        "blacklisted_command",
        "text_entry",
        "player_blips",
        "modified_fov",
        "invincibility",
        "fast_movement",
        "illegal_freeze",
        "illegal_vehicle_modifier",
        "bad_screen_word",
        "freecam_detected",
        "spiked_resource",
        "driving_hotwire",
        "illegal_vehicle_spawn"
    ];

    public function systemBans(): Response
    {
		$all = DB::select("SELECT COUNT(*) AS count, SUBSTRING_INDEX(reason, '-', 2) AS reason, SUM(playtime) / COUNT(*) as playtime FROM user_bans LEFT JOIN users ON license_identifier = identifier WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS', 'MEDIOCRE') GROUP BY SUBSTRING_INDEX(reason, '-', 2) LIMIT 20");
        $month = DB::select("SELECT COUNT(*) AS count, SUBSTRING_INDEX(reason, '-', 2) AS reason, SUM(playtime) / COUNT(*) as playtime FROM user_bans LEFT JOIN users ON license_identifier = identifier WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND timestamp >= " . (strtotime("-1 month")) . " AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS', 'MEDIOCRE') GROUP BY SUBSTRING_INDEX(reason, '-', 2) LIMIT 20");

		$graphData = $this->buildGraphData([], "SELECT timestamp FROM user_bans WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS')");

        $keys = array_keys($graphData);
        $min = $keys[0];
        $max = $keys[sizeof($keys) - 1];

		$image = $this->renderGraph(array_values($graphData), date("m/d/Y", $min) . ' - ' . date("m/d/Y", $max) . ' (7d avg)');

		$image = '<img src="' . $image . '" style="max-width: 100%; display: block; border: 1px solid #9CA3AF" />' . PHP_EOL;

        usort($all, function ($a, $b) {
            return $b->count - $a->count;
        });

        usort($month, function ($a, $b) {
            return $b->count - $a->count;
        });

        $allCount = array_reduce($all, function ($carry, $item) {
            return $carry + $item->count;
        }, 0);

        $monthCount = array_reduce($month, function ($carry, $item) {
            return $carry + $item->count;
        }, 0);

		$monthPlaytime = 0;

        $leaderboard = [];
        foreach ($month as $x => $ban) {
            $count = str_pad(number_format($ban->count), 6);

			$percentage = $ban->count / $monthCount;

			$monthPlaytime += $ban->playtime * $percentage;

            $percentage = str_pad(number_format(($percentage) * 100, 1) . "%", 6);
            $playtime = str_pad($this->formatSecondsMinimal($ban->playtime), 13);

            $leaderboard[] = str_pad(($x + 1) . "", 2, "0", STR_PAD_LEFT) . ". " . $percentage . " " . $count . " " . $playtime . " " . $ban->reason;
        }

		$totalPlaytime = 0;

        $leaderboard2 = [];
        foreach ($all as $x => $ban) {
            $count = str_pad(number_format($ban->count), 6);

			$percentage = $ban->count / $allCount;

			$totalPlaytime += $ban->playtime * $percentage;

            $percentage = str_pad(number_format(($percentage) * 100, 1) . "%", 6);
            $playtime = str_pad($this->formatSecondsMinimal($ban->playtime), 13);

            $leaderboard2[] = str_pad(($x + 1) . "", 2, "0", STR_PAD_LEFT) . ". " . $percentage . " " . $count . " " . $playtime . " " . $ban->reason;
        }

        $text = $image . "Last 30 days (" . $this->formatSecondsMinimal($monthPlaytime) . ")\n\n" . implode("\n", $leaderboard) . "\n\n- - -\n\nAll time (" . $this->formatSecondsMinimal($totalPlaytime) . ")\n\n" . implode("\n", $leaderboard2);

		return $this->fakeText(200, $text);
    }

    public function systemBansType(string $type): Response
    {
        if (!in_array($type, self::AntiCheatTypes)) {
            return $this->fakeText(404, "Invalid anti-cheat detection type");
        }

		$graphData = $this->buildGraphData([], "select anti_cheat_events.timestamp FROM anti_cheat_events LEFT JOIN user_bans ON license_identifier = identifier where type = '" . $type . "' AND ban_hash IS NOT NULL");
		$graphData = $this->buildGraphData($graphData, "select anti_cheat_events.timestamp FROM anti_cheat_events LEFT JOIN user_bans ON license_identifier = identifier where type = '" . $type . "' AND ban_hash IS NULL");

        $keys = array_keys($graphData);
        $min = $keys[0];
        $max = $keys[sizeof($keys) - 1];

		$image = $this->renderGraph(array_values($graphData), date("m/d/Y", $min) . ' - ' . date("m/d/Y", $max) . ' (7d avg)');

		$image = '<img src="' . $image . '" style="max-width: 100%; display: block; border: 1px solid #9CA3AF" />';

		return $this->fakeText(200, $image);
    }

    protected function buildGraphData($existingData, $query, $averageDays = 7)
    {
        $graph = DB::select($query);

        $index = sizeof($existingData);

		$graphDays = [];

		foreach($graph as $ban) {
			$day = strtotime(date("Y-m-d", $ban->timestamp));

			if(!isset($graphDays[$day])) {
				$graphDays[$day] = 0;
			}

			$graphDays[$day]++;
		}

		$min = empty($graphDays) ? (time() - 86400 * $averageDays) : min(array_keys($graphDays));
		$max = strtotime(date("Y-m-d"));

		for ($x = $min; $x <= $max; $x += 86400) {
			$start = $x - (86400 * $averageDays);

			$average = 0;

			for ($y = $start; $y <= $x; $y += 86400) {
				$average += $graphDays[$y] ?? 0;
			}

			$average /= $averageDays;

            if (!isset($existingData[$x])) {
                $existingData[$x] = [];
            }

            if ($index > 0) {
                for ($i = 0; $i < $index; $i++) {
                    $existingData[$x][$i] = $existingData[$x][$i] ?? 0;
                }
            }

			$existingData[$x][$index] = $average;
		}

        return $existingData;
    }
}
