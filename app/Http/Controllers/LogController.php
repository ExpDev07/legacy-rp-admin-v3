<?php

namespace App\Http\Controllers;

use App\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        // Begin querying the logs.
        $builder = Log::query()->latest()->limit(1000);

        // Filtering by player.
        if ($player = $request->get('player')) {
            $builder->where('identifier', $player);
        }

        return view('logs.index', [ 'logs' => $builder->paginate(20) ]);
    }

}
