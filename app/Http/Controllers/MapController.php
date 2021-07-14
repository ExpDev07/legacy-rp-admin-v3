<?php

namespace App\Http\Controllers;

use App\Player;
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
                'name' => Server::getServerName($rawServerIp),
            ];
        }

        $staff = Player::query()->where('is_staff', '=', true)->select(['steam_identifier'])->get()->toArray();

        return Inertia::render('Map/Index', [
            'servers' => $serverIps,
            'staff' => $staff ? array_map(function($player) {
                return $player['steam_identifier'];
            }, $staff) : []
        ]);
    }

}
