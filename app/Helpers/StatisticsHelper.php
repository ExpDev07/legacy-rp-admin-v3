<?php

namespace App\Helpers;

use App\Ban;
use App\CasinoLog;
use App\Character;
use App\Warning;
use DateTime;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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

        CacheHelper::write($key, $data, 30 * CacheHelper::MINUTE);

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

        CacheHelper::write($key, $data, 30 * CacheHelper::MINUTE);

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

        CacheHelper::write($key, $data, 30 * CacheHelper::MINUTE);

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

        $data = self::parseHistoricData($stats, false, false);

        CacheHelper::write($key, $data, 30 * CacheHelper::MINUTE);

        return $data;
    }

    /**
     * Returns user statistics
     *
     * @return array
     */
    public static function getUserStatistics(): array
    {
        $key = 'user_statistics';
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
        }

        $stats = DB::table('user_statistics')->select()->whereRaw("UNIX_TIMESTAMP(STR_TO_DATE(date, '%d.%m.%Y'))")->orderByRaw("UNIX_TIMESTAMP(STR_TO_DATE(date, '%d.%m.%Y'))")->get()->toArray();

        $data = [
            'data' => [
                [], [], []
            ],
            'labels' => []
        ];

        foreach ($stats as $stat) {
            $joined = json_decode($stat->joined_users, true) ?? [];

            $data['data'][0][] = $stat->total_joins;
            $data['data'][1][] = $stat->max_joined;
            $data['data'][2][] = sizeof($joined);
            $data['data'][3][] = $stat->max_queue;
            $data['labels'][] = $stat->date;
        }

        CacheHelper::write($key, $data, 15 * CacheHelper::MINUTE);

        return $data;
    }

    /**
     * Returns command statistics
     *
     * @return array
     */
    public static function getCommandStatistics(): array
    {
        $key = 'command_statistics';
        if (CacheHelper::exists($key)) {
            return CacheHelper::read($key, []);
        }

        $stats = DB::table('command_statistics')->select()->whereRaw("UNIX_TIMESTAMP(STR_TO_DATE(date, '%d.%m.%Y')) >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 MONTH))")->get()->toArray();

        $data = [
            'data' => [
                [], [], [], [], []
            ],
            'labels' => [],
			'tooltips' => []
        ];

        foreach ($stats as $stat) {
            $usage = json_decode($stat->usage, true) ?? [];

			$cleanedUsage = [];

			foreach ($usage as $command => $count) {
				$cleanedUsage[] = [
					'command' => $command,
					'count' => $count
				];
			}

			usort($cleanedUsage, function ($a, $b) {
				return $b['count'] <=> $a['count'];
			});

			$data['data'][4][] = sizeof($cleanedUsage) > 0 ? $cleanedUsage[0]['count'] : null;
			$data['data'][3][] = sizeof($cleanedUsage) > 1 ? $cleanedUsage[1]['count'] : null;
			$data['data'][2][] = sizeof($cleanedUsage) > 2 ? $cleanedUsage[2]['count'] : null;
			$data['data'][1][] = sizeof($cleanedUsage) > 3 ? $cleanedUsage[3]['count'] : null;
			$data['data'][0][] = sizeof($cleanedUsage) > 4 ? $cleanedUsage[4]['count'] : null;

            $data['labels'][] = $stat->date;

			$data['tooltips'][] = array_filter([
				sizeof($cleanedUsage) > 4 ? $cleanedUsage[4]['command'] . ": " . $cleanedUsage[4]['count'] : null,
				sizeof($cleanedUsage) > 3 ? $cleanedUsage[3]['command'] . ": " . $cleanedUsage[3]['count'] : null,
				sizeof($cleanedUsage) > 2 ? $cleanedUsage[2]['command'] . ": " . $cleanedUsage[2]['count'] : null,
				sizeof($cleanedUsage) > 1 ? $cleanedUsage[1]['command'] . ": " . $cleanedUsage[1]['count'] : null,
				sizeof($cleanedUsage) > 0 ? $cleanedUsage[0]['command'] . ": " . $cleanedUsage[0]['count'] : null,
			]);
        }

        CacheHelper::write($key, $data, 15 * CacheHelper::MINUTE);

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

        CacheHelper::write($key, $data, 30 * CacheHelper::MINUTE);

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

        $data = self::parseHistoricData($stats, false, false);

        CacheHelper::write($key, $data, 30 * CacheHelper::MINUTE);

        return $data;
    }

    /**
     * Returns Blackjack statistics
     *
     * @param string $license
     * @return array
     */
    public static function getBlackjackStats(string $license): array
    {
        $key = 'blackjack_statistics';

        $myPlace = self::getMyPlace(CasinoLog::GameBlackJack, $license);
        if (CacheHelper::exists($key)) {
            $data = CacheHelper::read($key, []);
            $data['my_place'] = $myPlace;

            return $data;
        }

        $data = self::casinoStatsForGame(CasinoLog::GameBlackJack);

        CacheHelper::write($key, $data, 1 * CacheHelper::HOUR);

        $data['my_place'] = $myPlace;

        return $data;
    }

    /**
     * Returns Slots statistics
     *
     * @param string $license
     * @return array
     */
    public static function getSlotsStats(string $license): array
    {
        $key = 'slots_statistics';

        $myPlace = self::getMyPlace(CasinoLog::GameSlots, $license);
        if (CacheHelper::exists($key)) {
            $data = CacheHelper::read($key, []);
            $data['my_place'] = $myPlace;

            return $data;
        }

        $data = self::casinoStatsForGame(CasinoLog::GameSlots);

        CacheHelper::write($key, $data, 1 * CacheHelper::HOUR);

        $data['my_place'] = $myPlace;

        return $data;
    }

    /**
     * Returns Tracks statistics
     *
     * @param string $license
     * @return array
     */
    public static function getTracksStats(string $license): array
    {
        $key = 'tracks_statistics';

        $myPlace = self::getMyPlace(CasinoLog::GameSlots, $license);
        if (CacheHelper::exists($key)) {
            $data = CacheHelper::read($key, []);
            $data['my_place'] = $myPlace;

            return $data;
        }

        $data = self::casinoStatsForGame(CasinoLog::GameTracks);

        CacheHelper::write($key, $data, 1 * CacheHelper::HOUR);

        $data['my_place'] = $myPlace;

        return $data;
    }

    private static function getMyPlace(string $game, string $staffLicenseIdentifier): ?array
    {
        $allBest = DB::table('casino_logs')
            ->where('game', '=', $game)
            ->whereRaw('`timestamp` > DATE_SUB(NOW(), INTERVAL 2 DAY)')
            ->selectRaw('SUM(IF(`money_won` < `bet_placed`, `money_won`, `money_won` - `bet_placed`)) as `win`, `casino_logs`.`license_identifier`')
            ->groupBy('license_identifier')
            ->orderByDesc('win')
            ->get()->toArray();

        $myPlace = null;
        foreach ($allBest as $place => $entry) {
            $entry = (array)$entry;

            if ($entry['license_identifier'] === $staffLicenseIdentifier) {
                $entry['place'] = $place + 1;
                $entry['total'] = sizeof($allBest);

                $myPlace = $entry;
                break;
            }
        }

        return $myPlace;
    }

    private static function casinoStatsForGame(string $game): array
    {
        $stats = DB::table('casino_logs')
            ->where('game', '=', $game)
            ->selectRaw(
                'MIN(`money_won`) as `min_earned`, ' .
                'MAX(`money_won`) as `max_earned`, ' .
                'SUM(`bet_placed`) / COUNT(`bet_placed`) as `average_spent`, ' .
                'SUM(`money_won`) / COUNT(`money_won`) as `average_earned`, ' .
                'SUM(`money_won`) / SUM(`bet_placed`) as `return_rate`, ' .
                'DATE_FORMAT(`timestamp`, \'%Y-%m-%d\') AS `day`'
            )
            ->groupByRaw('DATE_FORMAT(`timestamp`, \'%Y-%m-%d\')')
            ->orderBy('timestamp')
            ->get()->toArray();

        $best = DB::table('casino_logs')->fromSub(function ($q) use ($game) {
            $q->from('casino_logs')
                ->where('game', '=', $game)
                ->whereRaw('`timestamp` > DATE_SUB(NOW(), INTERVAL 2 DAY)')
                ->selectRaw('SUM(`money_won`) as `win`, `casino_logs`.`license_identifier`')
                ->groupBy('license_identifier')
                ->orderByDesc('win')
                ->limit(5);
        }, 'casino_logs')
            ->leftJoin('users', 'casino_logs.license_identifier', 'users.license_identifier')
            ->orderByDesc('win')
            ->get()->toArray();

        $worst = DB::table('casino_logs')->fromSub(function ($q) use ($game) {
            $q->from('casino_logs')
                ->where('game', '=', $game)
                ->whereRaw('`timestamp` > DATE_SUB(NOW(), INTERVAL 2 DAY)')
                ->selectRaw('SUM(`money_won`) as `win`, `casino_logs`.`license_identifier`')
                ->groupBy('license_identifier')
                ->orderBy('win')
                ->limit(5);
        }, 'casino_logs')
            ->leftJoin('users', 'casino_logs.license_identifier', 'users.license_identifier')
            ->orderBy('win')
            ->get()->toArray();

        return self::parseCasinoData($stats, $best, $worst);
    }

    private static function parseCasinoData(array $stats, array $best, array $worst): array
    {
        $data = [
            'labels'         => [],
            'min_earned'     => [],
            'max_earned'     => [],
            'average_spent'  => [],
            'average_earned' => [],
            'return_rate'    => [],

            'best_players'  => $best,
            'worst_players' => $worst,
        ];

        foreach ($stats as $row) {
            $data['labels'][] = $row->day;
            $data['min_earned'][] = floatval($row->min_earned);
            $data['max_earned'][] = floatval($row->max_earned);
            $data['average_spent'][] = floatval($row->average_spent);
            $data['average_earned'][] = floatval($row->average_earned);
            $data['return_rate'][] = floatval($row->return_rate) * 100;
        }

        return $data;
    }

    private static function parseHistoricData(array $stats, bool $doAverage = false, $lookbackTime = 30): array
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
			if (!$lookbackTime) {
				$lookbackTime = $days;
			}

            for ($x = $lookbackTime; $x >= 0; $x--) {
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

            if (!$d)
                var_dump($row);

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
