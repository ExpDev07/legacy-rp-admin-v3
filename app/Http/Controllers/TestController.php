<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\OPFWHelper;
use App\Log;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    public function reports(): Response
    {
        $reports = Log::query()
            ->selectRaw('`player_name`, COUNT(`identifier`) as `amount`')
            ->where('action', '=', 'Report')
            ->groupBy('identifier')
            ->leftJoin('users', 'identifier', '=', 'steam_identifier')
            ->orderByDesc('amount')
            ->get();

        $lines = [];

        foreach($reports as $report) {
            $lines[] = $report->player_name . ': ' . $report->amount;
        }

        return self::respond("Reports since last server restart:\n\n" . implode("\n", $lines));
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
