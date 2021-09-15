<?php

namespace App\Http\Controllers;

use App\Ban;
use App\BanStatistic;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use WhichBrowser\Cache;

class CronjobController extends Controller
{
    /**
     * Stores statistics for bans of the current day
     *
     * @param Request $request
     * @return Response
     */
    public function updateBanStatistics(Request $request): Response
    {
        if (!$this->validateRequest($request)) {
            return (new Response('Unauthorized', 401))->header('Content-Type', 'text/plain');
        }

        $date = date('Y-m-d');

        // Cleanup
        BanStatistic::query()->where('last_updated', '<', time() - CacheHelper::MONTH)->forceDelete();

        // Get count
        $current = intval(Ban::query()->selectRaw('COUNT(DISTINCT ban_hash) as count')->get()->first()['count']);

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
