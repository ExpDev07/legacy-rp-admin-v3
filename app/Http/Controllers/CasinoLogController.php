<?php

namespace App\Http\Controllers;

use App\CasinoLog;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;
use Inertia\Response;

class CasinoLogController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $start = round(microtime(true) * 1000);

        $query = CasinoLog::query()->orderByDesc('timestamp');

        // Filtering by identifier.
        if ($identifier = $request->input('identifier')) {
            $query->where('steam_identifier', '=', $identifier);
        }

        // Filtering by character.
        if ($character = $request->input('character')) {
            $query->where('character_id', '=', $character);
        }

        // Filtering by game.
        if ($game = $request->input('game')) {
            $query->where('game', '=', $game);
        }

        // Filtering by result.
        if ($result = $request->input('result')) {
            switch ($result) {
                case 'win':
                    $query->whereRaw('money_spent < money_earned');
                    break;
                case 'loss':
                    $query->whereRaw('money_earned < money_spent');
                    break;
                case 'draw':
                    $query->whereRaw('money_spent = money_earned');
                    break;
            }
        }

        $page = Paginator::resolveCurrentPage('page');

        $query->select(['id', 'steam_identifier', 'character_id', 'game', 'money_spent', 'money_earned', 'details', 'timestamp']);
        $query->limit(15)->offset(($page - 1) * 15);

        $logs = $query->get()->toArray();

        $end = round(microtime(true) * 1000);

        return Inertia::render('Casino/Index', [
            'logs'      => $logs,
            'filters'   => [
                'identifier' => $identifier,
                'character'  => $character,
                'game'       => $game ?? '',
                'result'     => $result ?? '',
            ],
            'links'     => $this->getPageUrls($page),
            'time'      => $end - $start,
            'playerMap' => Player::fetchSteamPlayerNameMap($logs, 'steam_identifier'),
            'page'      => $page,
        ]);
    }

}
