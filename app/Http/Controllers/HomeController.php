<?php

namespace App\Http\Controllers;

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
        if (Cache::store('file')->has('inspiring_quote')) {
            $quote = Cache::store('file')->get('inspiring_quote');
        } else {
            $json = json_decode(file_get_contents(__DIR__ . '/../../../helpers/quotes.json'), true);

            if ($json) {
                $quote = $json[array_rand($json)];
                Cache::store('file')->put('inspiring_quote', $quote, 12 * 60 * 60);
            } else {
                $quote = [
                    'quote'  => 'Quote machine broke',
                    'author' => 'Twoot',
                ];
            }
        }

        $data = $this->playerCount();
        $data['quote'] = $quote;

        return Inertia::render('Home', $data);
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
