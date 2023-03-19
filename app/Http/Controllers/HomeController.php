<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Helpers\GeneralHelper;
use App\Helpers\PermissionHelper;
use App\Helpers\OPFWHelper;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\Server;
use App\Player;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    /**
     * Renders the home page.
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        $quote = GeneralHelper::inspiring();
        $quote['quote'] = nl2br($quote['quote']);

        $user = $request->user();
        $identifier = $user->player->license_identifier;
        $name = $user->player->player_name;

        $bans = BanResource::collection(Ban::query()
            ->where(function ($query) use ($identifier, $name) {
                $query->orWhere('creator_identifier', '=', $identifier);
                $query->orWhere('creator_name', '=', $name);
            })
            ->where('identifier', 'LIKE', 'license:%')
            ->orderByDesc('timestamp')
            ->limit(8)->get())->toArray($request);

        $playerList = Player::getAllOnlinePlayers(true) ?? [];
        $playerList = array_filter($playerList, function($player) {
            return !$player['fakeName'] && !$player['fakeDisconnected'];
        });
        $players = array_keys($playerList);
        usort($players, function ($a, $b) use ($playerList) {
            return $playerList[$a]['id'] <=> $playerList[$b]['id'];
        });
        $staff = Player::query()->where(function ($q) {
            $q->orWhere('is_staff', '=', 1)
                ->orWhere('is_senior_staff', '=', 1)
                ->orWhere('is_super_admin', '=', 1)
                ->orWhereIn('license_identifier', GeneralHelper::getRootUsers());
        })->whereIn('license_identifier', $players)->get();

        return Inertia::render('Home', [
            'quote'       => $quote,
            'bans'        => $bans,
            'playerMap'   => Player::fetchLicensePlayerNameMap($bans, 'identifier'),
            'staff'       => PlayerIndexResource::collection($staff),
			'daysWithout' => $this->getDaysWithoutModdersImage()
        ]);
    }

	/**
	 * Sends a server announcement
	 *
     * @param Request $request
     * @return RedirectResponse
	 */
	protected function serverAnnouncement(Request $request)
	{
		if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_ANNOUNCEMENT)) {
            abort(401);
        }

		$message = trim($request->input('message'));

        if (empty($message)) {
            return back()->with('error', 'Message cannot be empty');
        }

		$response = OPFWHelper::serverAnnouncement(Server::getFirstServer() ?? '', $message);

        return $response->redirect();
	}

    /**
     * Returns player count info as json
     *
     * @return \Illuminate\Http\Response
     */
    public function playerCountApi(): \Illuminate\Http\Response
    {
        $data = $this->playerCount();

        return (new \Illuminate\Http\Response(json_encode($data), 200))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Returns player count info
     *
     * @return array|null
     */
    private function playerCount(): ?array
    {
        $totalPlayers = 0;
        $joinedPlayers = 0;
        $queuePlayers = 0;

        $data = Server::collectAllApiData();
        if (!$data) {
            return null;
        }

        foreach ($data as $server) {
            $totalPlayers += $server['total'];
            $joinedPlayers += $server['joined'];
            $queuePlayers += $server['queue'];
        }

        return [
            'totalPlayers'  => $totalPlayers,
            'joinedPlayers' => $joinedPlayers,
            'queuePlayers'  => $queuePlayers,
            'serverCount'   => sizeof($data),
        ];
    }

	private function getDaysWithoutModdersImage()
	{
		$ban = DB::select("SELECT timestamp FROM user_bans WHERE SUBSTRING_INDEX(reason, '-', 1) IN ('MODDING', 'INJECTION', 'NO_PERMISSIONS', 'ILLEGAL_VALUES', 'TIMEOUT_BYPASS') ORDER BY timestamp DESC LIMIT 1");

		if (!$ban) {
			return $this->renderDaysWithout(-1);
		}

		$ban = $ban[0];

		$days = floor((time() - $ban->timestamp) / 86400);

		return $this->renderDaysWithout($days);
	}

	private function renderDaysWithout(int $days)
	{
		$days = min($days, 99);

		$file = __DIR__ . '/../../../public/images/accident/accident_scaled.png';
		$font = __DIR__ . '/../../../public/images/accident/font.ttf';

		$image = imagecreatefrompng($file);

		$color = imagecolorallocate($image, 14, 37, 49);

		if ($days < 10) {
			if ($days === -1) {
				imagettftext($image, 32, 16, 388, 83, $color, $font, "X");
			} else {
				imagettftext($image, 32, 20, 388, 83, $color, $font, $days."");
			}
		} else {
			$decimal = floor($days / 10);
			$days = $days % 10;

			imagettftext($image, 29, 20, 381, 84, $color, $font, $decimal."");
			imagettftext($image, 29, 20, 397, 81, $color, $font, $days."");
		}

		ob_start();

		imagepng($image);

		$buffer = ob_get_contents();
		ob_end_clean();

		imagedestroy($image);

		return 'data:image/png;base64,' . base64_encode($buffer);
	}

}
