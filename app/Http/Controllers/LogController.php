<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Log;
use Illuminate\Http\Request;
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
    public function index(Request $request)
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


        return Inertia::render('Logs/Index', [
            'logs' => LogResource::collection($query->simplePaginate(15)),
        ]);
    }

}
