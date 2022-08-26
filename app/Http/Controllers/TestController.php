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
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
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
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
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
        $staff = Player::query()->select(["steam_identifier", "player_name"])->where("is_staff", "=", "1")->get();

        $staffMap = [];

        foreach ($staff as $player) {
            $staffMap[$player->steam_identifier] = $player->player_name;
        }

        // Haha this is ass
        $bans = DB::select("select identifier, creator_identifier, playtime, reason from user_bans LEFT JOIN users ON identifier = steam_identifier where identifier LIKE \"steam:%\" AND timestamp >= " . (strtotime("-3 months")) . " AND playtime > 0 AND (SELECT COUNT(*) FROM characters WHERE users.steam_identifier = characters.steam_identifier) > 0 AND creator_identifier IN ('" . implode("', '", array_keys($staffMap)) . "') ORDER BY playtime ASC LIMIT 100");

        $max = 0;
        for ($x = 0; $x < sizeof($bans) && $x < 10; $x++) {
            $ban = $bans[$x];

            $l = strlen($staffMap[$ban->creator_identifier]);

            if ($l > $max) {
                $max = $l;
            }
        }

        $fmt = function($s) {
            if ($s >= 60) {
                $m = floor($s / 60);
                $s -= $m * 60;

                return $m . "m " . $s . "s";
            }

            return $s . "s";
        };

        $leaderboard = [];
        for ($x = 0; $x < sizeof($bans) && $x < 10; $x++) {
            $ban = $bans[$x];

            $leaderboard[] = str_pad(($x+1)."", 2, "0", STR_PAD_LEFT) . ". " . str_pad($staffMap[$ban->creator_identifier], $max, " ") . "\t" . $ban->identifier . "\t" . $fmt(intval($ban->playtime)) . "\t" . ($ban->reason ?? "No reason");
        }

        $text = "Top 10 quickest bans (Last 3 months)\n\n" . implode("\n", $leaderboard);

        return self::respond($text);
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
