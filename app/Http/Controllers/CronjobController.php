<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Character;
use App\Helpers\CacheHelper;
use App\Player;
use App\Statistics\Statistic;
use App\Vehicle;
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
        CacheHelper::getLogActions(true);

        return (new Response('Success', 200))->header('Content-Type', 'text/plain');
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

        return $token && $token === $submitted;
    }
}
