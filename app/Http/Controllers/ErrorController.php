<?php

namespace App\Http\Controllers;

use App\Ban;
use App\ClientError;
use App\Helpers\CacheHelper;
use App\Helpers\GeneralHelper;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\PanelLog;
use App\Server;
use App\Player;
use App\ServerError;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ErrorController extends Controller
{

    /**
     * Renders the client errors page.
     *
     * @param Request $request
     * @return Response
     */
    public function client(Request $request): Response
    {
        $start = round(microtime(true) * 1000);

        $query = ClientError::query()->orderByDesc('timestamp');

        // Filtering by error_trace.
        if ($trace = $request->input('trace')) {
            $query->where('error_trace', 'LIKE', '%' . $trace . '%');
        }

        $cycle = intval($request->input('cycle')) ?? 0;
        if (!is_numeric($cycle) || $cycle < 0) {
            $cycle = 0;
        }

        $query->where('cycle_number', '=', $cycle);

        $page = Paginator::resolveCurrentPage('page');

        $query->groupByRaw('CONCAT(error_location, error_trace, FLOOR(timestamp / 300))');

        $query->selectRaw('cycle_number, error_id, steam_identifier, error_location, error_trace, error_feedback, full_trace, player_ping, server_id, timestamp, server_version, COUNT(error_id) as `occurrences`');
        $query->limit(15)->offset(($page - 1) * 15);

        $errors = $query->get()->toArray();

        $cycles = ClientError::query()
            ->selectRaw('cycle_number, MAX(timestamp) as first_occurence')
            ->where('cycle_number', '>', '0')
            ->groupBy('cycle_number')
            ->get()->toArray();

        $end = round(microtime(true) * 1000);

        return Inertia::render('Errors/Client', [
            'errors'    => $errors,
            'cycles'    => $cycles,
            'filters'   => [
                'trace' => $request->input('trace') ?? '',
                'cycle' => $cycle,
            ],
            'links'     => $this->getPageUrls($page),
            'playerMap' => Player::fetchSteamPlayerNameMap($errors, 'steam_identifier'),
            'time'      => $end - $start,
            'page'      => $page,
        ]);
    }

    /**
     * Renders the server errors page.
     *
     * @param Request $request
     * @return Response
     */
    public function server(Request $request): Response
    {
        $start = round(microtime(true) * 1000);

        $query = ServerError::query()->orderByDesc('timestamp');

        // Filtering by error_trace.
        if ($trace = $request->input('trace')) {
            $query->where('trace', 'LIKE', '%' . $trace . '%');
        }

        $cycle = intval($request->input('cycle')) ?? 0;
        if (!is_numeric($cycle) || $cycle < 0) {
            $cycle = 0;
        }

        $query->where('cycle_number', '=', $cycle);

        $page = Paginator::resolveCurrentPage('page');

        $query->select(['cycle_number', 'error_id', 'error_location', 'error_trace', 'server_id', 'timestamp']);
        $query->limit(15)->offset(($page - 1) * 15);

        $errors = $query->get()->toArray();

        $cycles = ServerError::query()
            ->selectRaw('cycle_number, MAX(timestamp) as first_occurence')
            ->where('cycle_number', '>', '0')
            ->groupBy('cycle_number')
            ->get()->toArray();

        $end = round(microtime(true) * 1000);

        return Inertia::render('Errors/Server', [
            'errors'  => $errors,
            'cycles'  => $cycles,
            'filters' => [
                'trace' => $request->input('trace') ?? '',
                'cycle' => $cycle,
            ],
            'links'   => $this->getPageUrls($page),
            'time'    => $end - $start,
            'page'    => $page,
        ]);
    }

    /**
     * Cycles the client errors.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function clientCycle(Request $request): \Illuminate\Http\Response
    {
        if (!GeneralHelper::isUserRoot($request->user()->player->steam_identifier)) {
            return self::json(false, null, 'Only root users can create a new cycle');
        }

        $current = ClientError::query()
            ->select('cycle_number')
            ->groupBy('cycle_number')
            ->orderByDesc('cycle_number')
            ->limit(1)
            ->get()->first();

        $current = $current ? intval($current->toArray()['cycle_number']) : 0;

        $next = $current + 1;

        ClientError::query()
            ->where('cycle_number', '=', '0')
            ->update([
                'cycle_number' => $next,
            ]);

        return self::json(true, $next);
    }

    /**
     * Cycles the server errors.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function serverCycle(Request $request): \Illuminate\Http\Response
    {
        if (!GeneralHelper::isUserRoot($request->user()->player->steam_identifier)) {
            return self::json(false, null, 'Only root users can create a new cycle');
        }

        $current = ServerError::query()
            ->select('cycle_number')
            ->groupBy('cycle_number')
            ->orderByDesc('cycle_number')
            ->limit(1)
            ->get()->first();

        $current = $current ? intval($current->toArray()['cycle_number']) : 0;

        $next = $current + 1;

        ServerError::query()
            ->where('cycle_number', '=', '0')
            ->update([
                'cycle_number' => $next,
            ]);

        return self::json(true, $next);
    }

}
