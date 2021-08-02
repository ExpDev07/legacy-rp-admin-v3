<?php

namespace App\Helpers;

use App\Ban;
use App\Character;
use App\Warning;
use Illuminate\Database\Eloquent\Model;
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
        }, 'warnings')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats);

        Cache::put($key, $data, 6 * 60 * 60);

        return $data;
    }

    /**
     * Returns Character creation statistics
     *
     * @return array
     */
    public static function getCharacterCreationStats(): array
    {
        $key = 'character_creation_statistics';
        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $stats = Character::query()->fromSub(function ($query) {
            $query->from('characters')->select([
                DB::raw('FROM_UNIXTIME(`character_creation_timestamp`, \'%Y-%m-%d\') AS `date`'),
            ])->whereNotNull('character_creation_timestamp')->orderByDesc('character_creation_timestamp');
        }, 'character')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats);

        Cache::put($key, $data, 6 * 60 * 60);

        return $data;
    }

    /**
     * Returns Lucky Wheel statistics
     *
     * @return array
     */
    public static function getLuckyWheelStats(): array
    {
        $key = 'lucky_wheel_spins_statistics';
        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $stats = DB::table('lucky_wheel_spins')->fromSub(function ($query) {
            $query->from('lucky_wheel_spins')->select([
                DB::raw('FROM_UNIXTIME(`timestamp`, \'%Y-%m-%d\') AS `date`'),
            ])->orderByDesc('timestamp');
        }, 'spins')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats);

        Cache::put($key, $data, 6 * 60 * 60);

        return $data;
    }

    /**
     * Returns Character creation statistics
     *
     * @return array
     */
    public static function getCharacterDeletionStats(): array
    {
        $key = 'character_deletion_statistics';
        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $stats = Character::query()->fromSub(function ($query) {
            $query->from('characters')->select([
                DB::raw('FROM_UNIXTIME(`character_deletion_timestamp`, \'%Y-%m-%d\') AS `date`'),
            ])->whereNotNull('character_deletion_timestamp')->orderByDesc('character_deletion_timestamp');
        }, 'character')->select([
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
            $row = (array)$row;
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
