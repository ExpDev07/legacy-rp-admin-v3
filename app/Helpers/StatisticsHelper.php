<?php

namespace App\Helpers;

use App\Ban;
use App\Character;
use App\Statistics\BanStatistic;
use App\Statistics\EconomyStatistic;
use App\Warning;
use DateTime;
use DateTimeZone;
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
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
        }

        $stats = Ban::query()->fromSub(function ($query) {
            $query->from('user_bans')->select([
                DB::raw('FROM_UNIXTIME(`timestamp`, \'%Y-%m-%d\') AS `date`'),
            ])->orderByDesc('timestamp')->groupBy('ban_hash');
        }, 'bans')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats, true);

        CacheHelper::write($key, $data, 6 * CacheHelper::HOUR);

        return $data;
    }

    /**
     * Returns Ban movement statistics
     *
     * @return array
     */
    public static function getBanMoveStats(): array
    {
        $key = 'ban_move_statistics';
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
        }

        $stats = BanStatistic::query()->select([
            'day', 'opening', 'closing', 'high', 'low',
        ])->get()->toArray();

        $data = self::formatFinanceStatistics($stats);

        CacheHelper::write($key, $data, 1 * CacheHelper::HOUR);

        return $data;
    }

    /**
     * Returns Economy statistics
     *
     * @return array
     */
    public static function getEconomyStats(): array
    {
        $key = 'economy_statistics';
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
        }

        $stats = EconomyStatistic::query()->select([
            'day', 'opening', 'closing', 'high', 'low',
        ])->get()->toArray();

        $data = self::formatFinanceStatistics($stats);

        CacheHelper::write($key, $data, 1 * CacheHelper::HOUR);

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
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
        }

        $stats = Warning::query()->fromSub(function ($query) {
            $query->from('warnings')->select([
                DB::raw('FROM_UNIXTIME(UNIX_TIMESTAMP(`created_at`), \'%Y-%m-%d\') AS `date`'),
            ])->whereIn('warning_type', [Warning::TypeWarning, Warning::TypeStrike])->orderByDesc('created_at');
        }, 'warnings')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats, true);

        CacheHelper::write($key, $data, 6 * CacheHelper::HOUR);

        return $data;
    }

    /**
     * Returns Notes statistics
     *
     * @return array
     */
    public static function getNoteStats(): array
    {
        $key = 'note_statistics';
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
        }

        $stats = Warning::query()->fromSub(function ($query) {
            $query->from('warnings')->select([
                DB::raw('FROM_UNIXTIME(UNIX_TIMESTAMP(`created_at`), \'%Y-%m-%d\') AS `date`'),
            ])->where('warning_type', '=', Warning::TypeNote)->orderByDesc('created_at');
        }, 'warnings')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $data = self::parseHistoricData($stats, true);

        CacheHelper::write($key, $data, 6 * CacheHelper::HOUR);

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
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
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

        CacheHelper::write($key, $data, 6 * CacheHelper::HOUR);

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
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
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

        CacheHelper::write($key, $data, 6 * CacheHelper::HOUR);

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
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
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

        CacheHelper::write($key, $data, 6 * CacheHelper::HOUR);

        return $data;
    }

    private static function parseHistoricData(array $stats, bool $doAverage = false): array
    {
        $map = [];
        $earliest = time();
        foreach ($stats as $row) {
            $row = (array)$row;
            $map[$row['date']] = $row['count'];

            $time = strtotime($row['date']);
            if ($time < $earliest) {
                $earliest = $time;
            }
        }

        $days = ceil((time() - $earliest) / 86400);

        $complete = [];
        if ($doAverage) {
            for ($x = $days; $x >= 0; $x--) {
                $day = date('Y-m-d', strtotime('-' . $x . ' day'));
                $t = date('M Y', strtotime('-' . $x . ' day'));

                if (!isset($complete[$t])) {
                    $complete[$t] = [
                        'value' => 0,
                        'count' => 0,
                    ];
                }

                if (isset($map[$day])) {
                    $complete[$t]['value'] += $map[$day];
                }

                $complete[$t]['count']++;
            }

            foreach ($complete as &$item) {
                $item = round($item['value'] / $item['count']);
            }
        } else {
            for ($x = 30; $x >= 0; $x--) {
                $t = date('Y-m-d', strtotime('-' . $x . ' day'));
                if (!isset($map[$t])) {
                    $complete[$t] = 0;
                } else {
                    $complete[$t] = $map[$t];
                }
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

    private static function formatFinanceStatistics(array $stats): array
    {
        $data = [
            'labels' => [],
            'data'   => [],
        ];
        foreach ($stats as $row) {
            $data['labels'][] = $row['day'];
            $d = DateTime::createFromFormat(
                'Y-m-d',
                $row['day'],
                new DateTimeZone('UTC')
            );

            $data['data'][] = [
                'x' => $d->getTimestamp() * 1000,
                'o' => $row['opening'],
                'h' => $row['high'],
                'l' => $row['low'],
                'c' => $row['closing'],
            ];
        }

        return $data;
    }
}
