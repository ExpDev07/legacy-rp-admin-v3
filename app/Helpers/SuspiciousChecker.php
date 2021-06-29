<?php

namespace App\Helpers;

use App\Character;
use App\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SuspiciousChecker
{
    /**
     * Items that cannot be obtained naturally
     */
    const UnusualItems = [
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
        $items = self::UnusualItems;
        $key = 'unusual_items_' . md5(json_encode($items));

        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $entries = Log::query()
            ->where('action', '=', 'Item Moved')
            ->where(function ($query) use ($items) {
                foreach ($items as $item) {
                    $query->orWhere('details', 'LIKE', '%x ' . $item . ' to%');
                }
            })->select([
                'identifier', 'details', 'timestamp',
            ])->orderByDesc('timestamp')
            ->get()->toArray();

        Cache::put($key, $entries, 30 * 60);

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
            ->where(DB::raw('`cash`+`bank`'), '>=', 1250000)
            ->orWhere('stocks_balance', '>=', 1000000)
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

        $logs = Log::query()
            ->where('action', '=', 'Used Pawn Shop')
            ->select(['details', 'timestamp'])
            ->orderByDesc('timestamp')
            ->get()->toArray();

        $eval = [];
        foreach ($logs as $log) {
            $re = '/] \((steam:.+?)\).+?received \$(\d+)/mi';
            preg_match($re, $log['details'], $match);

            if (sizeof($match) !== 3) {
                continue;
            }

            $identifier = $match[1];
            $cash = intval($match[2]);
            $time = ceil(strtotime($log['timestamp']) / (5 * 60)) * (5 * 60);

            if (!isset($eval[$time])) {
                $eval[$time] = [];
            }
            if (!isset($eval[$time][$identifier])) {
                $eval[$time][$identifier] = 0;
            }

            $eval[$time][$identifier] += $cash;
        }

        $sus = [];
        foreach ($eval as $time => $ids) {
            foreach ($ids as $id => $cash) {
                if ($cash > 100000) {
                    $sus[] = [
                        'identifier' => $id,
                        'details'    => 'Sold jewelry worth $' . number_format($cash) . ' in the span of 5 minutes',
                        'timestamp'  => date('c', $time),
                    ];
                }
            }
        }

        Cache::put($key, $sus, 30 * 60);

        return $sus;
    }
}
