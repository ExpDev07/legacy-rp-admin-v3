<?php

namespace App\Http\Controllers;

use App\Helpers\SuspiciousChecker;
use App\Http\Resources\LogResource;
use App\Log;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SuspiciousController extends Controller
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

        $logs = [];
        $map = ['none' => 'none'];
        $type = null;

        // Filtering by type.
        if ($type = $request->input('logType')) {
            switch ($type) {
                case 'items':
                    $logs = SuspiciousChecker::findInvalidItems();
                    $map = Player::fetchSteamPlayerNameMap($logs, 'identifier');
                    break;
                case 'characters':
                    $logs = SuspiciousChecker::findSuspiciousCharacters();
                    $map = Player::fetchSteamPlayerNameMap($logs, 'steam_identifier');
                    break;
                case 'pawn':
                    $logs = SuspiciousChecker::findSuspiciousPawnShopUsages();
                    $map = Player::fetchSteamPlayerNameMap($logs, 'identifier');
                    break;
            }
        }

        $type = $type ?? '';

        $page = Paginator::resolveCurrentPage('page');

        $logs = array_slice($logs, ($page - 1) * 15, 15);

        $end = round(microtime(true) * 1000);

        return Inertia::render('Suspicious/Index', [
            'logs'      => $logs,
            'filters'   => [
                'logType' => $type,
            ],
            'links'     => $this->getPageUrls($page),
            'time'      => $end - $start,
            'page'      => $page,
            'logType'   => $type,
            'playerMap' => $map,
        ]);
    }

}
