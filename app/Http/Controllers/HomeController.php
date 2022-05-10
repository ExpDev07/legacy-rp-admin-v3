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
        $identifier = $user->player->steam_identifier;
        $name = $user->player->player_name;

        $bans = BanResource::collection(Ban::query()
            ->where(function ($query) use ($identifier, $name) {
                $query->orWhere('creator_identifier', '=', $identifier);
                $query->orWhere('creator_name', '=', $name);
            })
            ->where('identifier', 'LIKE', 'steam:%')
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
        $staff = Player::query()->where('is_staff', '=', true)->whereIn('steam_identifier', $players)->get();

        return Inertia::render('Home', [
            'quote'     => $quote,
            'bans'      => $bans,
            'playerMap' => Player::fetchSteamPlayerNameMap($bans, 'identifier'),
            'staff'     => PlayerIndexResource::collection($staff),
        ]);
    }

    /**
     * Renders the staff chat.
     *
     * @param Request $request
     * @return Response
     */
    public function staff(Request $request): Response
    {
        return Inertia::render('StaffChat', []);
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

}
