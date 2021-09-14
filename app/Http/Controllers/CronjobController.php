<?php

namespace App\Http\Controllers;

use App\Ban;
use App\BanStatistic;
use App\Helpers\GeneralHelper;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\Server;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CronjobController extends Controller
{
    /**
     * Stores statistics for bans of the current day
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateBanStatistics(Request $request): \Illuminate\Http\Response
    {
        if (!$this->validateRequest($request)) {
            return (new \Illuminate\Http\Response('Unauthorized', 401))->header('Content-Type', 'text/plain');
        }

        $date = date('Y-m-d');

        $current = Ban::query()->groupBy('ban_hash')->count('ban_hash');

        /**
         * @var $today BanStatistic|null
         */
        $today = BanStatistic::query()->where('day', '=', $date)->get()->first();
        if (!$today) {
            BanStatistic::query()->create([
                'last_updated' => time(),
                'day'          => $date,
                'opening'      => $current,
                'closing'      => $current,
                'high'         => $current,
                'low'          => $current,
            ]);
        } else {
            if ($today->high < $current) {
                $today->high = $current;
            }
            if ($today->low > $current) {
                $today->low = $current;
            }

            $today->closing = $current;
            $today->last_updated = time();

            $today->update();
        }

        return (new \Illuminate\Http\Response('Success', 200))->header('Content-Type', 'text/plain');
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
