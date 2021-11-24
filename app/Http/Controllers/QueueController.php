<?php

namespace App\Http\Controllers;

use App\Helpers\OPFWHelper;
use App\Server;
use App\Player;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QueueController extends Controller
{
    /**
     * Renders the queue page.
     *
     * @param Request $request
     * @param string $server
     * @return Response
     */
    public function render(Request $request, string $server): Response
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            abort(401, 'Only super admins can view the current queue.');
        }

        if (!Server::getServerApiURLFromName($server)) {
            abort(404, 'Unknown server.');
        }

        return Inertia::render('Queue/Index', [
            'server' => $server,
        ]);
    }

    /**
     * Makes a player skip the queue
     *
     * @param Request $request
     * @param string $server
     * @param string $steamIdentifier
     * @return \Illuminate\Http\Response
     */
    public function skip(Request $request, string $server, string $steamIdentifier): \Illuminate\Http\Response
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return self::json(false, null, 'Only super admins can make players skip the queue.');
        }

        $serverIp = Server::getServerApiURLFromName($server);
        if (!$serverIp) {
            return self::json(false, null, 'Unknown server.');
        }

        $response = OPFWHelper::updateQueuePosition($serverIp, $steamIdentifier, 0);

        if ($response->status) {
            return self::json(true, $response->message);
        }

        return self::json(false, null, $response->message ?? 'Failed to set players queue position.');
    }

    /**
     * Queue api
     *
     * @param Request $request
     * @param string $server
     * @return \Illuminate\Http\Response
     */
    public function api(Request $request, string $server): \Illuminate\Http\Response
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return self::json(false, null, 'Only super admins can view the current queue.');
        }

        $serverIp = Server::getServerApiURLFromName($server);
        if (!$serverIp) {
            return self::json(false, null, 'Unknown server.');
        }

        $queue = OPFWHelper::getQueueJSON($serverIp) ?? [];

        return self::json(true, [
            'queue'     => $queue,
            'playerMap' => Player::fetchSteamPlayerNameMap($queue, ['steamIdentifier']),
        ]);
    }

}
