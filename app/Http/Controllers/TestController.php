<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\OPFWHelper;
use App\Log;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
	const FinancialResources = [
		"diamonds" => [5000, 6000],
		"gold_watches" => [1250, 1500],
		"necklaces" => [500, 600],
		"silver_watches" => [300, 350],
		"gold_bar" => 1000,

		"raw_emerald" => [50, 140],
		"raw_sapphire" => [140, 260],
		"raw_ruby" => [270, 530],
		"raw_morganite" => [1400, 2320],

		"emerald" => [140, 230],
		"sapphire" => [270, 520],
		"ruby" => [540, 1000],
		"morganite" => [2220, 5530],
	];

    public function logs(Request $request, string $action): Response
    {
        $action = trim($action);

        if (!$action) {
            return self::respond("Empty action!");
        }

        $details = trim($request->input('details'));

        $all = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', $action);

        if ($details) {
            $all->where('details', 'LIKE', '%' . $details . '%');
        }

        $all = $all->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'license_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $last24hours = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', $action);

        if ($details) {
            $last24hours->where('details', 'LIKE', '%' . $details . '%');
        }

        $last24hours = $last24hours->where(DB::raw('UNIX_TIMESTAMP(`timestamp`)'), '>', time() - 24 * 60 * 60)
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'license_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $text = self::renderStatistics($action, "24 hours", $last24hours, $details);
        $text .= "\n\n";
        $text .= self::renderStatistics($action, "30 days", $all, $details);

        return self::respond($text);
    }

    private static function renderStatistics(string $type, string $timespan, $rows, $details): string
    {
        $lines = [
            "Top 10 Logs of type `" . $type . "` in the past " . $timespan . ":",
            $details ? "- Details like: `" . $details . "`\n" : "",
        ];

        foreach ($rows as $message) {
            $lines[] = $message->player_name . ': ' . $message->amount;
        }

        return implode("\n", $lines);
    }

    public function smartWatchLeaderboard(): Response
    {
        $all = DB::table('inventories')
            ->select('item_metadata')
            ->where('item_name', '=', 'smart_watch')
            ->get()
            ->toArray();

        $leaderboard = [];

        foreach ($all as $item) {
            $metadata = json_decode($item->item_metadata, true);

            if ($metadata && isset($metadata['firstName']) && isset($metadata['lastName'])) {
                $name = $metadata['firstName'] . ' ' . $metadata['lastName'];

                if (!isset($leaderboard[$name])) {
                    $leaderboard[$name] = [
                        'steps' => 0,
                        'deaths' => 0
                    ];
                }

                if (isset($metadata['stepsWalked'])) {
                    $steps = floor(floatval($metadata['stepsWalked']));

                    if ($leaderboard[$name]['steps'] < $steps) {
                        $leaderboard[$name]['steps'] = $steps;
                    }
                }

                if (isset($metadata['deaths'])) {
                    $deaths = intval($metadata['deaths']);

                    if ($leaderboard[$name]['deaths'] < $deaths) {
                        $leaderboard[$name]['deaths'] = $deaths;
                    }
                }
            }
        }

        $list = [];

        foreach ($leaderboard as $name => $data) {
            $list[] = [
                'name' => $name,
                'steps' => $data['steps'],
                'deaths' => $data['deaths']
            ];
        }

        usort($list, function ($a, $b) {
            return $b['steps'] - $a['steps'];
        });

        $index = 0;

        $stepsList = array_map(function ($entry) use (&$index) {
            $index++;

            return $index . ".\t" . number_format($entry['steps']) . "\t" . $entry['name'];
        }, array_splice($list, 0, 15));

        usort($list, function ($a, $b) {
            return $b['deaths'] - $a['deaths'];
        });

        $index = 0;

        $deathsList = array_map(function ($entry) use (&$index) {
            $index++;

            return $index . ".\t" . number_format($entry['deaths']) . "\t" . $entry['name'];
        }, array_splice($list, 0, 15));

        $text = "Top 15 steps traveled\n\nSpot\tSteps\tFull-Name\n" . implode("\n", $stepsList);
        $text .= "\n\n- - -\n\n";
        $text .= "Top 15 deaths\n\nSpot\tDeaths\tFull-Name\n" . implode("\n", $deathsList);

        return self::respond($text);
    }

    public function banLeaderboard(): Response
    {
        $staff = Player::query()->select(["license_identifier", "player_name"])->where("is_staff", "=", "1")->orWhere("is_senior_staff", "=", "1")->orWhere("is_super_admin", "=", "1")->get();

        $max = 0;
        $staffMap = [];

        foreach ($staff as $player) {
            $staffMap[$player->license_identifier] = $player->player_name;

            if (strlen($player->player_name) > $max) {
                $max = strlen($player->player_name);
            }
        }

		if (strlen("System") > $max) {
			$max = strlen("System");
		}

        // What a chonker
        $query = "SELECT * FROM (SELECT identifier, creator_identifier, reason, (SELECT SUM(playtime) FROM characters WHERE license_identifier = identifier) as playtime FROM user_bans WHERE identifier LIKE 'license:%' AND creator_identifier IN ('" . implode("', '", array_keys($staffMap)) . "') AND timestamp >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 DAY))) bans WHERE playtime IS NOT NULL AND playtime > 0 ORDER BY playtime LIMIT 10";
        $querySystem = "SELECT * FROM (SELECT identifier, creator_identifier, reason, (SELECT SUM(playtime) FROM characters WHERE license_identifier = identifier) as playtime FROM user_bans WHERE identifier LIKE 'license:%' AND creator_name IS NULL AND timestamp >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 DAY))) bans WHERE playtime IS NOT NULL AND playtime > 0 ORDER BY playtime LIMIT 1";

        $bans = DB::select($query);
        $banSystem = DB::select($querySystem);

        $fmt = function ($s) {
            if ($s >= 60) {
                $m = floor($s / 60);
                $s -= $m * 60;

                return $m . "m " . $s . "s";
            }

            return $s . "s";
        };

        $leaderboard = [];

		$banSystem = $banSystem[0];
		$leaderboard[] = "00. " . str_pad("System", $max, " ") . "  " . $banSystem->identifier . "\t" . $fmt(intval($banSystem->playtime)) . "\t" . ($banSystem->reason ?? "No reason");

        for ($x = 0; $x < sizeof($bans) && $x < 10; $x++) {
            $ban = $bans[$x];

			$name = $staffMap[$ban->creator_identifier] ?? "System";

            $leaderboard[] = str_pad(($x + 1) . "", 2, "0", STR_PAD_LEFT) . ". " . str_pad($name, $max, " ") . "  " . $ban->identifier . "\t" . $fmt(intval($ban->playtime)) . "\t" . ($ban->reason ?? "No reason");
        }

        $bans = DB::select("SELECT COUNT(identifier) c, creator_identifier FROM user_bans WHERE identifier LIKE \"license:%\" AND timestamp >= " . (strtotime("-3 months")) . " AND (creator_identifier IN ('" . implode("', '", array_keys($staffMap)) . "') OR creator_name IS NULL) GROUP BY creator_identifier ORDER BY c DESC");

		$days = round((time() - strtotime("-3 months")) / 86400);

        $leaderboard2 = [];
        for ($x = 0; $x < sizeof($bans) && $x <= 10; $x++) {
            $ban = $bans[$x];

			$perDay = round($ban->c / $days, 1);

			$name = $staffMap[$ban->creator_identifier] ?? "System";

            $leaderboard2[] = str_pad($x . "", 2, "0", STR_PAD_LEFT) . ". " . str_pad($name, $max, " ") . "  " . str_pad($ban->c . " bans", 10, " ") . " (~" . $perDay . " per day)";
        }

        $text = "Top 10 quickest bans (Last 3 months)\n\n" . implode("\n", $leaderboard) . "\n\n- - -\n\nTop 10 most bans (Last 3 months)\n\n" . implode("\n", $leaderboard2);

        if (isset($_GET["all"])) {
            $bans = DB::select("SELECT COUNT(identifier) c, creator_identifier FROM user_bans WHERE identifier LIKE \"license:%\" AND (creator_identifier IN ('" . implode("', '", array_keys($staffMap)) . "') OR creator_name IS NULL) GROUP BY creator_identifier ORDER BY c DESC");

            $leaderboard3 = [];
            foreach ($bans as $x => $ban) {
				$name = $staffMap[$ban->creator_identifier] ?? "System";

                $leaderboard3[] = str_pad(($x + 1) . "", 2, "0", STR_PAD_LEFT) . ". " . str_pad($name, $max, " ") . "  " . $ban->c . " bans";
            }

            $text .= "\n\n- - -\n\nTop 10 most bans (All time)\n\n" . implode("\n", $leaderboard3);
        }

        return self::respond($text);
    }

    private function formatSecondsMinimal($seconds)
    {
        $seconds = floor($seconds);

        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;

        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $time = "";

        if ($hours > 0) {
            $time .= $hours . "h ";
        }

        if ($minutes > 0) {
            $time .= $minutes . "m ";
        }

        if ($seconds > 0) {
            $time .= $seconds . "s";
        }

        return "~" . $time;
    }

	private function renderTimestampGraph($query)
	{
		$graph = DB::select($query);

		$graphDays = [];

		foreach($graph as $ban) {
			$day = strtotime(date("Y-m-d", $ban->timestamp));

			if(!isset($graphDays[$day])) {
				$graphDays[$day] = 0;
			}

			$graphDays[$day]++;
		}

		$min = empty($graphDays) ? (time() - 86400 * 7) : min(array_keys($graphDays));
		$max = strtotime(date("Y-m-d"));

		$averageData = [];

		for ($x = $min; $x <= $max; $x += 86400) {
			$start = $x - (86400 * 7);

			$average = 0;

			for ($y = $start; $y <= $x; $y += 86400) {
				$average += $graphDays[$y] ?? 0;
			}

			$average /= 7;

			$averageData[] = $average;
		}

		$image = $this->renderGraph($averageData, date("m/d/Y", $min) . ' - ' . date("m/d/Y", $max) . ' (7d avg)');

		return '<img src="' . $image . '" style="max-width: 100%; display: block; border: 1px solid #9CA3AF" />' . "\n\n";
	}

    public function systemBans(): Response
    {
		$all = DB::select("SELECT COUNT(*) AS count, SUBSTRING_INDEX(reason, '-', 2) AS reason, SUM(playtime) / COUNT(*) as playtime FROM user_bans LEFT JOIN users ON license_identifier = identifier WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS', 'MEDIOCRE') GROUP BY SUBSTRING_INDEX(reason, '-', 2) LIMIT 20");
        $month = DB::select("SELECT COUNT(*) AS count, SUBSTRING_INDEX(reason, '-', 2) AS reason, SUM(playtime) / COUNT(*) as playtime FROM user_bans LEFT JOIN users ON license_identifier = identifier WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND timestamp >= " . (strtotime("-1 month")) . " AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS', 'MEDIOCRE') GROUP BY SUBSTRING_INDEX(reason, '-', 2) LIMIT 20");

		$image = $this->renderTimestampGraph("SELECT timestamp FROM user_bans WHERE creator_name IS NULL AND SUBSTRING_INDEX(identifier, ':', 1) = 'license' AND SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS')");

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

    public function moddingBans(Request $request): Response
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return self::respond('Only super admins can export bans.');
        }

        $keywords = [
            "cheat",
            "modder",
            "modding",
            "script",
            "hacker",
            "hacking",
            "inject"
        ];

        foreach ($keywords as &$word) {
            $word = "reason like \"%" . $word . "%\"";
        }

        $query = "select identifier, reason from user_bans where identifier like \"license:%\" and (" . implode(" or ", $keywords);

        if (CLUSTER === "c3") {
            $query .= " or (reason like \"%1.5%\" and timestamp > 1614553200)";
        }

        $query .= ") GROUP BY identifier ORDER BY timestamp";

        $bans = DB::select($query);

        $fd = fopen('php://temp/maxmemory:1048576', 'w');

        fputcsv($fd, ["license_identifier", "reason"]);

        foreach ($bans as $ban) {
            fputcsv($fd, [$ban->identifier, $ban->reason]);
        }

        rewind($fd);
        $csv = stream_get_contents($fd);
        fclose($fd);

        return (new Response($csv, 200))
            ->header('Content-Type', 'application/octet-stream')
            ->header("Content-Transfer-Encoding", "Binary")
            ->header("Content-disposition", "attachment; filename=\"modders.csv\"");
    }

    public function staffPlaytime(Request $request): Response
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return self::respond('Only super admins can do this.');
        }

        $staff = Player::query()->select(["license_identifier", "player_name", "playtime"])->orWhere("is_staff", "=", "1")->orWhere("is_senior_staff", "=", "1")->orWhere("is_super_admin", "=", "1")->get();

        $entries = [];

        foreach ($staff as $player) {
            $entries[] = [
                'license' => $player->license_identifer,
                'name' => $player->player_name,
                'playtime' => intval($player->playtime)
            ];
        }

        usort($entries, function ($a, $b) {
            return $b['playtime'] - $a['playtime'];
        });

        $text = "Staff playtime\n\n";

        foreach ($entries as $entry) {
            $seconds = $entry['playtime'];

            $minutes = floor($seconds / 60);
            $seconds -= $minutes * 60;

            $hours = floor($minutes / 60);
            $minutes -= $hours * 60;

            $time = str_pad($hours . "h " . $minutes . "m " . $seconds . "s", 12);

            $text .= $time . " - " . $entry['name'] . " (" . $entry['license'] . ")\n";
        }

        return self::respond($text);
    }

    public function jobApi(Request $request, string $api_key, string $jobName, string $departmentName, string $positionName, string $characterIds): Response
    {
        if (env('DEV_API_KEY', '') !== $api_key || empty($api_key)) {
            return (new Response('Unauthorized', 403))->header('Content-Type', 'text/plain');
        }

        $characterIds = explode(',', $characterIds);

        if (empty($characterIds)) {
            return (new Response('No character_ids provided', 400))->header('Content-Type', 'text/plain');
        }

        $characters = Character::query()
            ->select(["license_identifier", "character_id", "job_name", "department_name", "position_name", "first_name", "last_name"])
            ->whereIn('character_id', $characterIds)
            ->orWhere(function ($query) use ($jobName, $departmentName, $positionName) {
                return $query->where('job_name', $jobName)
                    ->where('department_name', $departmentName)
                    ->where('position_name', $positionName);
            })
            ->get()->toArray();

        return (new Response(json_encode($characters), 200))->header('Content-Type', 'application/json');
    }

    public function finance(Request $request): Response
    {
        $data = DB::select(DB::raw("SELECT SUM(cash + bank + stocks_balance) as total_money FROM characters"));
		$money = floor($data[0]->total_money);

        $data = DB::select(DB::raw("SELECT SUM(amount) as total_shared from shared_accounts"));
		$money += floor($data[0]->total_shared);

        $data = DB::select(DB::raw("SELECT SUM(company_balance) as total_stocks FROM stocks_companies"));
		$money += floor($data[0]->total_stocks);

        $data = DB::select(DB::raw("SELECT SUM(1) as count, item_name FROM inventories WHERE item_name IN ('" . implode("', '", array_keys(self::FinancialResources)) . "') GROUP BY item_name"));
		$resources = 0;

		foreach ($data as $item) {
			$price = self::FinancialResources[$item->item_name];

			if (is_array($price)) {
				$price = ($price[0] + $price[1]) / 2;
			}

			$resources += $price * $item->count;
		}

		$text = [
			"In circulation: $" . number_format($money),
			"In valuables:   $" . number_format($resources),
			"",
			"Total:          $" . number_format($money + $resources)
		];

        return self::respond(implode("\n", $text));
    }

    /**
     * Responds with plain text
     *
     * @param string $data
     * @return Response
     */
    private static function respond(string $data): Response
    {
        return (new Response($data, 200))->header('Content-Type', 'text/plain');
    }
}
