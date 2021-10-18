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

        $page = Paginator::resolveCurrentPage('page');

        $query->groupByRaw('CONCAT(error_location, error_trace, FLOOR(timestamp / 300))');

        $query->selectRaw('error_id, steam_identifier, error_location, error_trace, error_feedback, player_ping, server_id, timestamp, COUNT(error_id) as `occurrences`');
        $query->limit(15)->offset(($page - 1) * 15);

        $errors = $query->get()->toArray();

        $end = round(microtime(true) * 1000);

        return Inertia::render('Errors/Client', [
            'errors'    => $errors,
            'filters'   => $request->all(
                'trace'
            ),
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

        $page = Paginator::resolveCurrentPage('page');

        $query->select(['error_id', 'error_location', 'error_trace', 'server_id', 'timestamp']);
        $query->limit(15)->offset(($page - 1) * 15);

        $errors = $query->get()->toArray();

        $end = round(microtime(true) * 1000);

        return Inertia::render('Errors/Server', [
            'errors'    => $errors,
            'filters'   => $request->all(
                'trace'
            ),
            'links'     => $this->getPageUrls($page),
            'time'      => $end - $start,
            'page'      => $page,
        ]);
    }

}
