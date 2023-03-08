<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Helpers\PermissionHelper;
use App\Http\Resources\LogResource;
use App\Log;
use App\Player;
use App\Server;
use App\Character;
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
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_SCREENSHOT)) {
            abort(401);
        }

        return Inertia::render('Overwatch/Index');
    }

    /**
     * Get a screenshot and some data belonging to it from a random player.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getRandomScreenshot(Request $request): \Illuminate\Http\Response
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_SCREENSHOT)) {
            return self::json(false, null, 'You can not use the screenshot functionality');
        }

        $players = Player::getAllOnlinePlayers(true);

        $players = array_filter($players, function($player) {
            return $player && $player['character'] && !GeneralHelper::isUserRoot($player['license']);
        });

        if (!empty($players)) {
            $license = array_rand($players);
            $player = $players[$license];

			$character = Character::query()->where('character_id', '=', $player['character'])->first();

			if ($character) {
				$screenshotResponse = OPFWHelper::createScreenshot($player['server'], $player['id']);

				if ($screenshotResponse->status) {
					return self::json(true, [
						"license"   => $license,
						"url"       => $screenshotResponse->data['screenshotURL'],
						"id"        => $player['id'],
						"server"    => Server::getServerName($player['server']),
						"character" => [
							"name" => $character->first_name . ' ' . $character->last_name,
							"id"   => $character->character_id
						]
					]);
				} else {
					return self::json(false, null, "Failed to obtain a screenshot of the player.");
				}
			} else {
				return self::json(false, null, "Failed to get character info of the player.");
			}
        } else {
            return self::json(false, null, "There are no players available.");
        }
    }
}
