<?php

namespace App\Http\Controllers;

use App\Helpers\OPFWHelper;
use App\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $rawServerIps = explode(',', env('OP_FW_SERVERS', ''));
        $serverIps = [];
        foreach ($rawServerIps as $index => $rawServerIp) {
            $serverIps[] = [
                'id'   => $index,
                'name' => Server::getServerName($rawServerIp),
            ];
        }

        return Inertia::render('Map/Index', [
            'servers' => $serverIps,
        ]);
    }

    /**
     * Cached data api
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request): \Illuminate\Http\Response
    {
        $server = intval($request->query('server', 0)) ?? 0;

        $data = [];
        $serverIps = explode(',', env('OP_FW_SERVERS', ''));
        if ($serverIps) {
            $ip = $serverIps[0];
            if (isset($serverIps[$server])) {
                $ip = $serverIps[$server];
            }

            $data = OPFWHelper::getWorldJSON($ip);
        }

        return (new \Illuminate\Http\Response(json_encode($data), 200))
            ->header('Content-Type', 'application/json');
    }

}
