<?php

namespace App\Helpers;

use App\Character;
use App\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SuspiciousChecker
{
    /**
     * Non stackable items that cannot be obtained naturally
     */
    const SingleUnusualItems = [
        'Flare Gun',
        'Up-n-Atomizer',
        'Ceramic Pistol',
        'Navy Revolver',
        'Unholy Hellbringer',
        'MG',
        'Combat MG',
        'Combat MG Mk II',
        'RPG',
        'Grenade Launcher',
        'Grenade Launcher Smoke',
        'Minigun',
        'Firework Launcher',
        'Railgun',
        'Homing Launcher',
        'Compact Grenade',
        'Widowmaker',
        'Grenade',
        'BZ Gas',
        'Molotov Cocktail',
        'Sticky Bomb',
        'Proximity Mines',
        'Pipe Bombs',
        'Tear Gas',
        'Flare',
    ];

    /**
     * Finds items that cant be obtained in the city
     *
     * @return array
     */
    public static function findInvalidItems(): array
    {
        $items = self::SingleUnusualItems;
        $key = 'unusual_items_' . md5(json_encode($items));

        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $sql = "SELECT `identifier`, `details`, `timestamp` FROM `user_logs` WHERE action = 'Item Moved' AND SUBSTRING_INDEX(SUBSTRING_INDEX(details, ' moved ', -1), ' to ', 1) IN ('1x " . implode('\', \'1x ', $items) . "')";

        $entries = json_decode(json_encode(DB::select($sql)), true);

        Cache::put($key, $entries, 10 * 60);

        return $entries;
    }

    /**
     * Finds characters who have an unusual amount of money
     *
     * @return array
     */
    public static function findSuspiciousCharacters(): array
    {
        return Character::query()
            ->where(DB::raw('`cash`+`bank`'), '>=', 1000000)
            ->orWhere('stocks_balance', '>=', 500000)
            ->select(['steam_identifier', 'character_id', 'cash', 'bank', 'stocks_balance', 'first_name', 'last_name'])
            ->get()->toArray();
    }

    /**
     * Finds entries where a lot of jewelry has been sold at a pawn shop
     *
     * @return array
     */
    public static function findSuspiciousPawnShopUsages(): array
    {
        $key = 'pawn_transactions';

        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $sql = "SELECT SUM(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, '$', -1), '.', 1)) as `cash`, CEIL(UNIX_TIMESTAMP(`timestamp`) / 300) * 300 as `time`, `identifier` FROM `user_logs` WHERE action = 'Used Pawn Shop' GROUP BY CONCAT(`identifier`, '|', `time`) ORDER BY `time` DESC";

        $sus = self::getSaleLogEntries($sql, 100000, 'jewelry');

        Cache::put($key, $sus, 10 * 60);

        return $sus;
    }

    /**
     * Finds entries where a lot of materials have been sold at the warehouse
     *
     * @return array
     */
    public static function findSuspiciousWarehouseUsages(): array
    {
        $key = 'warehouse_transactions';

        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $sql = "SELECT SUM(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, '$', -1), '.', 1)) as `cash`, CEIL(UNIX_TIMESTAMP(`timestamp`) / 300) * 300 as `time`, `identifier` FROM `user_logs` WHERE action = 'Sold materials' GROUP BY CONCAT(`identifier`, '|', `time`) ORDER BY `time` DESC";

        $sus = self::getSaleLogEntries($sql, 10000, 'materials');

        Cache::put($key, $sus, 10 * 60);

        return $sus;
    }

    /**
     * Parses [cash, identifier, time] arrays
     *
     * @param string $sql
     * @param int $threshold
     * @param string $name
     * @return array
     */
    private static function getSaleLogEntries(string $sql, int $threshold, string $name): array
    {
        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $logs = DB::select($sql);

        $sus = [];
        foreach ($logs as $log) {
            $cash = intval($log->cash);

            if ($cash > $threshold) {
                $sus[] = [
                    'identifier' => $log->identifier,
                    'details'    => 'Sold ' . $name . ' worth $' . number_format($cash) . ' in the span of 5 minutes',
                    'timestamp'  => date('c', intval($log->time)),
                ];
            }
        }

        return $sus;
    }
}
