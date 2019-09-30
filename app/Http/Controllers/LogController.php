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
     * @return Response
     */
    public function index()
    {
        return view('logs.index', [ 'logs' => Log::query()->latest()->simplePaginate(20) ]);
    }

}
