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
    public function reports(): Response
    {
        $allReports = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', 'Report')
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $reports24hours = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', 'Report')
            ->where(DB::raw('UNIX_TIMESTAMP(`timestamp`)'), '>', time() - 24*60*60)
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $text = self::renderStatistics("Reports", "24 hours", $reports24hours);
        $text .= "\n\n";
        $text .= self::renderStatistics("Reports", "30 days", $allReports);

        return self::respond($text);
    }

    public function localOOC(): Response
    {
        $allOOC = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', 'Local OOC message')
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $ooc24hours = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', 'Local OOC message')
            ->where(DB::raw('UNIX_TIMESTAMP(`timestamp`)'), '>', time() - 24*60*60)
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
            ->orderByDesc('amount')
            ->limit(10)
            ->get();

        $text = self::renderStatistics("Local OOC Messages", "24 hours", $ooc24hours);
        $text .= "\n\n";
        $text .= self::renderStatistics("Local OOC Messages", "30 days", $allOOC);

        return self::respond($text);
    }

    private static function renderStatistics(string $type, string $timespan, $rows): string
    {
        $lines = [
            "Top 10 " . $type . " in the past " . $timespan . ":",
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
