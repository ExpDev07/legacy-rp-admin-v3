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

        $lines = [
            "Top 10 Reports in the past 30 days:",
            "",
        ];

        foreach($allReports as $report) {
            $lines[] = $report->player_name . ': ' . $report->amount;
        }

        $lines[] = "";
        $lines[] = "Top 10 reports in the past 24 hours";
        $lines[] = "";

        foreach($reports24hours as $report) {
            $lines[] = $report->player_name . ': ' . $report->amount;
        }

        return self::respond(implode("\n", $lines));
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
