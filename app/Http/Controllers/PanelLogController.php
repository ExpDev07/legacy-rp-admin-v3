<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Log;
use App\PanelLog;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PanelLogController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $start = round(microtime(true) * 1000);

        $query = PanelLog::query()->orderByDesc('timestamp');

        // Filtering by source_identifier.
        if ($source = $request->input('source')) {
            $query->where('source_identifier', $source);
        }

        // Filtering by target_identifier.
        if ($target = $request->input('target')) {
            $query->where('target_identifier', $target);
        }

        // Filtering by action.
        if ($action = $request->input('action')) {
            if (Str::startsWith($action, '=')) {
                $action = Str::substr($action, 1);
                $query->where('action', $action);
            } else {
                $query->where('action', 'like', "%{$action}%");
            }
        }

        // Filtering by log.
        if ($log = $request->input('log')) {
            if (Str::startsWith($log, '=')) {
                $log = Str::substr($log, 1);
                $query->where('log', $log);
            } else {
                $query->where('log', 'like', "%{$log}%");
            }
        }

        $page = Paginator::resolveCurrentPage('page');

        $query->select(['id', 'source_identifier', 'target_identifier', 'timestamp', 'log', 'action']);
        $query->limit(15)->offset(($page - 1) * 15);

        $logs = $query->get()->toArray();

        $end = round(microtime(true) * 1000);

        return Inertia::render('PanelLogs/Index', [
            'logs'      => $logs,
            'filters'   => $request->all(
                'source',
                'target',
                'action',
                'log'
            ),
            'links'     => $this->getPageUrls($page),
            'time'      => $end - $start,
            'playerMap' => Player::fetchSteamPlayerNameMap($logs, ['source_identifier', 'target_identifier']),
            'page'      => $page,
        ]);
    }

}
