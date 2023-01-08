<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Helpers\GeneralHelper;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\Server;
use App\Player;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use SteamID;

class SteamLookupController extends Controller
{

    /**
     * Renders the steam lookup page.
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        return Inertia::render('Steam');
    }

    /**
     * Returns player info from steam.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function playerInfo(Request $request): \Illuminate\Http\Response
    {
		$search = trim($request->input('search'));

		$error = false;
		$steamId = false;

		if (!empty($search)) {
			if (Str::startsWith($search, "https://steamcommunity.com/id/")) {
				$html = GeneralHelper::get($search);

				if ($html) {
					$re = '/<script type="text\/javascript">\s+g_rgProfileData = ({.+?});/m';

					preg_match($re, $html, $matches);

					if (sizeof($matches) > 1) {
						$profile = json_decode($matches[1]);

						if (isset($profile->steamid)) {
							$steamId = $profile->steamid;
						} else {
							$error = "Unable to get steam id from profile.";
						}
					} else {
						$error = "Steam returned an invalid response.";
					}
				} else {
					$error = "Unable to get steam profile.";
				}
			} else if (Str::startsWith($search, "https://steamcommunity.com/profiles/")) {
				$re = '/https:\/\/steamcommunity\.com\/profiles\/(\d+)/m';

				preg_match($re, $search, $matches);

				if (sizeof($matches) > 1) {
					$steamId = intval($matches[1]);
				} else {
					$error = "Invalid steam profile url.";
				}
			} else {
				try {
					$id = new SteamID($search);

					$steamId = $id->ConvertToUInt64();
				} catch (Exception $ex) {
					$error = "Invalid search value.";
				}
			}
		} else {
			$error = "Please enter a steam id or profile url.";
		}

		$data = [
			'error' => $error
		];

		if ($steamId) {
			$data['steamId'] = $steamId;

			try {
				$id = new SteamID($steamId);

				$data['steamHex'] = "steam:" . dechex($id->ConvertToUInt64());

				$data['steam2'] = $id->RenderSteam2();
				$data['steam3'] = $id->RenderSteam3();

				$data['invite'] = 'http://s.team/p/' . $id->RenderSteamInvite();
			} catch (Exception $ex) {
				$data['error'] = "Invalid steam id.";
			}
		}

        return (new \Illuminate\Http\Response(json_encode($data), 200))
            ->header('Content-Type', 'application/json');
    }

}
