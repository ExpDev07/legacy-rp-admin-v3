<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Log;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LogController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $query = Log::query()->orderByDesc('timestamp');

        // Filtering by identifier.
        if ($identifier = $request->input('identifier')) {
            $query->where('identifier', $identifier);
        }

        // Filtering by server.
        if ($server = $request->input('server')) {
            $query->where('metadata->serverId', $server);
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

        // Filtering by details.
        if ($details = $request->input('details')) {
            if (Str::startsWith($details, '=')) {
                $details = Str::substr($details, 1);
                $query->where('details', $details);
            } else {
                $query->where('details', 'like', "%{$details}%");
            }
        }

        $query->select(['id', 'identifier', 'action', 'details', 'metadata', 'timestamp']);

        $logs = LogResource::collection($query->paginate(15, [
            'id',
        ])->appends($request->query()));

        return Inertia::render('Logs/Index', [
            'logs'      => $logs,
            'filters'   => $request->all(
                'identifier',
                'server',
                'action',
                'details'
            ),
            'playerMap' => Player::fetchSteamPlayerNameMap($logs->toArray($request), 'steamIdentifier'),
        ]);
    }

}
