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
    public function logs(string $action): Response
    {
        $action = trim($action);

        if (!$action) {
            return self::respond("Empty action!");
        }

        $all = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', $action)
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $last24hours = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', $action)
            ->where(DB::raw('UNIX_TIMESTAMP(`timestamp`)'), '>', time() - 24*60*60)
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $text = self::renderStatistics($action, "24 hours", $last24hours);
        $text .= "\n\n";
        $text .= self::renderStatistics($action, "30 days", $all);

        return self::respond($text);
    }

    private static function renderStatistics(string $type, string $timespan, $rows): string
    {
        $lines = [
            "Top 10 Logs of type `" . $type . "` in the past " . $timespan . ":",
            "",
        ];

        foreach($rows as $message) {
            $lines[] = $message->player_name . ': ' . $message->amount;
        }

        return implode("\n", $lines);
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
