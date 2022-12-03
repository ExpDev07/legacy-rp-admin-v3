<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Helpers\GeneralHelper;
use App\Helpers\StatisticsHelper;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\Server;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StatisticsController extends Controller
{

    /**
     * Renders the home page.
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request): Response
    {
        $steam = $request->user()->player->steam_identifier;

        return Inertia::render('Statistics/Index', [
            'bans'       => StatisticsHelper::getBanStats(),
            'warnings'   => StatisticsHelper::getWarningStats(),
            'notes'      => StatisticsHelper::getNoteStats(),
            'creations'  => StatisticsHelper::getCharacterCreationStats(),
            'deletions'  => StatisticsHelper::getCharacterDeletionStats(),
            'userStatistics'  => StatisticsHelper::getUserStatistics(),
            'luckyWheel' => StatisticsHelper::getLuckyWheelStats(),
            'blackjack'  => StatisticsHelper::getBlackjackStats($steam),
            'tracks'     => StatisticsHelper::getTracksStats($steam),
            'slots'      => StatisticsHelper::getSlotsStats($steam),
        ]);
    }

}
