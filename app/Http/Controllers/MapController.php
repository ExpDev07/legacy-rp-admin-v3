<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Helpers\PermissionHelper;
use App\Player;
use App\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\SessionHelper;

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
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_LIVEMAP)) {
            abort(401);
        }

        $rawServerIps = explode(',', env('OP_FW_SERVERS', ''));
        $serverIps = [];
        foreach ($rawServerIps as $index => $rawServerIp) {
            $serverIps[] = [
                'name' => Server::getServerName($rawServerIp),
            ];
        }

        $staff = Player::query()->where('is_staff', '=', true)->select(['steam_identifier', 'player_name'])->get()->toArray();

        $marker = $request->query('m') ?? null;
        if ($marker) {
            $xy = explode(',', $marker);

            if (sizeof($xy) == 2 && is_numeric($xy[0]) && is_numeric($xy[1])) {
                $marker = [
                    floatval($xy[0]),
                    floatval($xy[1])
                ];
            } else {
                $marker = null;
            }
        }

        return Inertia::render('Map/Index', [
            'servers'  => $serverIps,
            'staff'    => $staff ? array_map(function ($player) {
                return $player['steam_identifier'];
            }, $staff) : [],
            'staffMap' => $staff,
            'blips'    => GeneralHelper::parseMapFile(__DIR__ . '/../../../helpers/markers.map') ?? [],
            'token'    => SessionHelper::getInstance()->getSessionKey(),
            'cluster'  => CLUSTER,
            'myself'   => $request->user()->player->steam_identifier,
            'marker'   => $marker
        ]);
    }

}
