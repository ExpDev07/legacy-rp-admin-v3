<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Character;
use App\Helpers\CacheHelper;
use App\Player;
use App\Statistics\BanStatistic;
use App\Statistics\EconomyStatistic;
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

        // Get count
        $current = intval(Ban::query()->selectRaw('COUNT(DISTINCT ban_hash) as count')->get()->first()['count']);

        // Update and cleanup
        $this->updateStatistic(new BanStatistic(), $current);

        return (new Response('Success', 200))->header('Content-Type', 'text/plain');
    }

    /**
     * Stores statistics for the economy of the current day
     *
     * @param Request $request
     * @return Response
     */
    public function updateEconomyStatistics(Request $request): Response
    {
        if (!$this->validateRequest($request)) {
            return (new Response('Unauthorized', 401))->header('Content-Type', 'text/plain');
        }

        // Get count
        $current = intval(Character::query()->selectRaw('SUM(`cash` + `bank` + `stocks_balance`) as `sum`')->get()->first()['sum']);
        $current += intval(DB::table('stocks_companies')->selectRaw('SUM(`company_balance`) as `sum`')->get()->first()->sum);
        $current += intval(DB::table('shared_accounts')->selectRaw('SUM(`amount`) as `sum`')->whereIn('id', [1, 2])->get()->first()->sum);
        $current += Vehicle::getTotalVehicleValue(null);

        // Update and cleanup
        $this->updateStatistic(new EconomyStatistic(), $current);

        return (new Response('Success', 200))->header('Content-Type', 'text/plain');
    }

    private function updateStatistic(Statistic $statistic, int $current)
    {
        $date = date('Y-m-d');
        $statistic::query()->where('last_updated', '<', time() - 12 * CacheHelper::MONTH)->forceDelete();

        /**
         * @var $today Statistic|null
         */
        $today = $statistic::query()->where('day', '=', $date)->get()->first();
        if (!$today) {
            $statistic::query()->create([
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
