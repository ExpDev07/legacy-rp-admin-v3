<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Log;
use App\Player;
use App\Server;
use App\Helpers\OPFWHelper;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OverwatchController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Overwatch/Index');
    }

    /**
     * Get a screenshot and some data belonging to it from a random player.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRandomScreenshot(): \Illuminate\Http\Response
    {
        $players = Player::getAllOnlinePlayers(true);

        if (!empty($players)) {
            $steam = array_rand($players);
            $player = $players[$steam];

            $screenshotResponse = OPFWHelper::createScreenshot($player['server'], $player['id']);

            if ($screenshotResponse->status) {
                return self::json(true, [
                    "steam"  => $steam,
                    "url"    => $screenshotResponse->data['screenshotURL'],
                    "id"     => $player['id'],
                    "server" => Server::getServerName($player['server'])
                ]);
            } else {
                return self::json(false, null, "Failed to obtain a screenshot of the player.");
            }
        } else {
            return self::json(false, null, "There are no players available.");
        }
    }
}
