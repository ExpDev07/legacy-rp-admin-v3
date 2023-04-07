<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Character;
use App\Helpers\CacheHelper;
use App\Helpers\SessionHelper;
use App\Player;
use App\Statistics\Statistic;
use App\Vehicle;
use App\Server;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CronjobController extends Controller
{
    /**
     * General purpose cronjobs
     *
     * @return Response
     */
    public function generalCronjob(): Response
    {
        $start = microtime(true);
        echo "Getting log actions...";
        CacheHelper::getLogActions(true);

        echo $this->stopTime($start);

        $start = microtime(true);
        echo "Getting server status...";
        CacheHelper::getServerStatus(Server::getFirstServer(), true);

        echo $this->stopTime($start);

        $start = microtime(true);
        echo "Cleaning up sessions...";
        SessionHelper::cleanup();

        echo $this->stopTime($start);

        return (new Response('Success', 200))->header('Content-Type', 'text/plain');
    }

    private function stopTime($time): string {
        return round(microtime(true) - $time, 2) . "s" . PHP_EOL;
    }

    /**
     * Validates a request
     *
     * @param Request $request
     * @return bool
     */
    private function validateRequest(Request $request): bool
    {
        $token = env('DEV_API_KEY', '');
        $submitted = $request->query('token');

        return $token && $token !== "some_random_token" && $token === $submitted;
    }
}
