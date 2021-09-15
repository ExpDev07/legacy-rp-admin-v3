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
        return Inertia::render('Statistics/Index', [
            'bans'       => StatisticsHelper::getBanStats(),
            'banMove'    => StatisticsHelper::getBanMoveStats(),
            'warnings'   => StatisticsHelper::getWarningStats(),
            'creations'  => StatisticsHelper::getCharacterCreationStats(),
            'deletions'  => StatisticsHelper::getCharacterDeletionStats(),
            'luckyWheel' => StatisticsHelper::getLuckyWheelStats(),
        ]);
    }

}
