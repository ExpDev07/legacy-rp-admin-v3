<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Server;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{

    /**
     * Renders the home page.
     *
     * @return Response
     */
    public function render(): Response
    {
        $quote = GeneralHelper::inspiring();
        $quote['quote'] = nl2br($quote['quote']);

        return Inertia::render('Home', [
            'quote' => $quote,
        ]);
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
     * @return array
     */
    private function playerCount(): array
    {
        $totalPlayers = 0;
        $joinedPlayers = 0;
        $queuePlayers = 0;

        $data = Server::collectAllApiData();
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
