<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Log;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
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
            $query->where('action','like', "%{$action}%");
        }

        // Filtering by details.
        if ($details = $request->input('details')) {
            $query->where('details', 'like', "%{$details}%");
        }

        $query->leftJoin('users', 'identifier', '=', 'steam_identifier');
        $query->select(['id', 'identifier', 'action', 'details', 'metadata', 'timestamp', 'player_name']);

        return Inertia::render('Logs/Index', [
            'logs' => LogResource::collection($query->paginate(15, [
                'id'
            ])->appends($request->query())),
            'filters' => $request->all(
                'identifier',
                'server',
                'action',
                'details'
            ),
        ]);
    }

}
