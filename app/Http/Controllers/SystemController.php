<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    const AntiCheatTypes = [
        "bad_screen_word",
        "blacklisted_command",
        "damage_modifier",
        "distance_taze",
        "driving_hotwire",
        "fast_movement",
        "freecam_detected",
        "illegal_event",
        "illegal_freeze",
        "illegal_server_event",
        "illegal_vehicle_modifier",
        "illegal_vehicle_spawn",
        "illegal_weapon",
        "invincibility",
        "modified_fov",
        "ped_change",
        "ped_spawn",
        "player_blips",
        "runtime_texture",
        "spawned_object",
        "spectate",
        "spiked_resource",
        "text_entry",
        "thermal_night_vision",
        "vehicle_modification",
    ];

    public function systemBans(): Response
    {
		$all = DB::select("SELECT COUNT(*) AS count, SUBSTRING_INDEX(reason, '-', 2) AS reason, SUM(playtime) / COUNT(*) as playtime FROM user_bans LEFT JOIN users ON license_identifier = identifier WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS', 'MEDIOCRE') GROUP BY SUBSTRING_INDEX(reason, '-', 2) LIMIT 20");
        $month = DB::select("SELECT COUNT(*) AS count, SUBSTRING_INDEX(reason, '-', 2) AS reason, SUM(playtime) / COUNT(*) as playtime FROM user_bans LEFT JOIN users ON license_identifier = identifier WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND timestamp >= " . (strtotime("-1 month")) . " AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS', 'MEDIOCRE') GROUP BY SUBSTRING_INDEX(reason, '-', 2) LIMIT 20");

		$graphData = $this->buildGraphData([], "SELECT timestamp FROM user_bans WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS')");

        $keys = array_keys($graphData);
        $min = min($keys);
        $max = max($keys);

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
            return $this->fakeText(404, "Invalid anti-cheat detection type (" . implode(", ", self::AntiCheatTypes) . ")");
        }

		$graphData = $this->buildGraphData([], "select anti_cheat_events.timestamp FROM anti_cheat_events LEFT JOIN user_bans ON license_identifier = identifier where type = '" . $type . "' AND ban_hash IS NOT NULL", 1);
		$graphData = $this->buildGraphData($graphData, "select anti_cheat_events.timestamp FROM anti_cheat_events LEFT JOIN user_bans ON license_identifier = identifier where type = '" . $type . "' AND ban_hash IS NULL", 1);

        if (empty($graphData)) {
            return $this->fakeText(404, "No data available");
        }

        $keys = array_keys($graphData);
        $min = min($keys);
        $max = max($keys);

		$image = $this->renderGraph(array_values($graphData), $type . ': ' . date("m/d/Y", $min) . ' - ' . date("m/d/Y", $max));

		$image = '<img src="' . $image . '" style="max-width: 100%; display: block; border: 1px solid #9CA3AF" />';

		return $this->fakeText(200, $image);
    }

    protected function buildGraphData($existingData, $query, $averageDays = 7)
    {
        $graph = DB::select($query);

        $index = !empty($existingData) ? sizeof(array_values($existingData)[0]) : 0;

		$graphDays = [];

		foreach($graph as $ban) {
			$day = strtotime(date("Y-m-d", $ban->timestamp));

			if(!isset($graphDays[$day])) {
				$graphDays[$day] = 0;
			}

			$graphDays[$day]++;
		}

		$min = empty($graphDays) ? (time() - 86400 * 10) : min(array_keys($graphDays));
		$max = strtotime(date("Y-m-d"));

        $min2 = !empty($existingData) ? min(array_keys($existingData)) : null;

        if ($min2 && $min2 < $min) {
            $min = $min2;
        }

		for ($day = $min; $day <= $max; $day += 86400) {
			$offset = $day - (86400 * $averageDays);

			$average = 0;

			for ($offset; $offset <= $day; $offset += 86400) {
				$average += $graphDays[$offset] ?? 0;
			}

			$average /= $averageDays;

            if (!isset($existingData[$day])) {
                $existingData[$day] = [];
            }

            while (sizeof($existingData[$day]) < $index) {
                $existingData[$day][] = 0;
            }

			$existingData[$day][] = $average;
		}

        return $existingData;
    }
}
