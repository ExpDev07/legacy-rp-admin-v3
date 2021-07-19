<?php

namespace App\Helpers;

use App\Ban;
use App\Warning;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatisticsHelper
{
    /**
     * Returns Ban statistics
     *
     * @return array
     */
    public static function getBanStats(): array
    {
        $key = 'ban_statistics';
        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $stats = Ban::query()->fromSub(function ($query) {
            $query->from('user_bans')->select([
                DB::raw('FROM_UNIXTIME(`timestamp`, \'%Y-%m-%d\') AS `date`'),
            ])->orderByDesc('timestamp')->groupBy('ban_hash');
        }, 'bans')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats);

        Cache::put($key, $data, 6 * 60 * 60);

        return $data;
    }
    /**
     * Returns Warning statistics
     *
     * @return array
     */
    public static function getWarningStats(): array
    {
        $key = 'warning_statistics';
        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $stats = Warning::query()->fromSub(function ($query) {
            $query->from('warnings')->select([
                DB::raw('FROM_UNIXTIME(UNIX_TIMESTAMP(`created_at`), \'%Y-%m-%d\') AS `date`'),
            ])->orderByDesc('created_at');
        }, 'bans')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats);

        Cache::put($key, $data, 6 * 60 * 60);

        return $data;
    }

    private static function parseHistoricData(array $stats): array
    {
        $map = [];
        foreach ($stats as $row) {
            $map[$row['date']] = $row['count'];
        }

        $complete = [];
        for ($x = 30; $x >= 0; $x--) {
            $t = date('Y-m-d', strtotime('-' . $x . ' day'));
            if (!isset($map[$t])) {
                $complete[$t] = 0;
            } else {
                $complete[$t] = $map[$t];
            }
        }

        $data = [
            'labels' => [],
            'data'   => [],
        ];
        foreach ($complete as $label => $value) {
            $data['labels'][] = $label;
            $data['data'][] = $value;
        }

        return $data;
    }
}
