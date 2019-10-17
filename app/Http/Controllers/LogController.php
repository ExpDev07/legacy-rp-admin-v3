<?php

namespace App\Http\Controllers;

use App\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogController extends Controller
{

    /**
     * The logs.
     *
     * @var Log
     */
    private $logs;

    /**
     * Constructs a new LogController.
     *
     * @param Log $logs
     */
    public function __construct(Log $logs)
    {
        $this->logs = $logs;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Begin querying the logs.
        $builder = $this->logs->newQuery();

        // Filtering by player.
        if ($player = $request->get('player')) {
            $builder->where('identifier', $player);
        }

        return view('logs.index', [ 'logs' => $builder->simplePaginate(25) ]);
    }

}
