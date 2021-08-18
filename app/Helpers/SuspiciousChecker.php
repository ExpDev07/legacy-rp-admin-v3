<?php

namespace App\Helpers;

use App\Character;
use App\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SuspiciousChecker
{
    const CacheTime = 10 * 60;

    const IgnoreUnusualMovement = [
        'Snowballs'      => 1500,
        'Water'          => 1500,
        'Weed 1oz'       => 800,
        'Weed 1q'        => 800,
        'Cocaine Bag'    => 800,
        'Acid'           => 800,
        'Fertilizer'     => 500,
        'Sub Ammo'       => 750,
        'Rifle Ammo'     => 750,
        'Shotgun Ammo'   => 500,
        'Cheeseburger'   => 900,
        'Hamburger'      => 900,
        'Thermite'       => 500,
        'Coke'           => 900,
        'Banana'         => 900,
        'First Aid Kit'  => 400,
        'Weed Seeds'     => 800,
        'Joint'          => 800,
        'Cigarette'      => 800,
        'Scrap Metal'    => 800,
        'Glass'          => 800,
        'Pistol Ammo'    => 600,
        'Aluminium'      => 600,
        'Steel'          => 600,
        'Rubber'         => 600,
        'Bucket'         => 800,
        'Silver Watches' => 350,
        'Gold Watches'   => 350,
        'Necklaces'      => 500,
    ];

    const IgnoreItems = [
        // Food
        'water'              => 800,
        'hamburger'          => 800,
        'belgian_fries'      => 800,
        'coke'               => 800,
        'wonder_waffle'      => 800,
        'cheeseburger'       => 800,
        'cheesecake'         => 800,
        'donut'              => 800,
        'green_apple'        => 800,
        'sandwich'           => 800,
        'taco'               => 800,
        'banana'             => 800,
        'smores'             => 800,

        // Drugs are irrelevant cause people hoard them
        'acid'               => 250,
        'cocaine_bag'        => 250,
        'weed_seeds'         => 250,
        'weed_1q'            => 250,
        'weed_1oz'           => 250,
        'oxy'                => 250,

        // Ammo same deal as with drugs
        'pistol_ammo'        => 250,
        'rifle_ammo'         => 250,
        'shotgun_ammo'       => 250,
        'sub_ammo'           => 250,

        // Materials cuz mechanics
        'scrap_metal'        => 350,
        'rubber'             => 350,
        'steel'              => 350,

        // Some general stuff people just like to hoard
        'evidence_bag_empty' => 500,
        'fertilizer'         => 250,
        'paper_bag'          => 500,
        'weapon_snowball'    => 800,
    ];

    /**
     * Items ids that cannot be obtained naturally
     */
    const UnusualItems = [
        'weapon_unarmed',
        'weapon_flaregun',
        'weapon_raypistol',
        'weapon_ceramicpistol',
        'weapon_navyrevolver',
        'weapon_raycarbine',
        'weapon_mg',
        'weapon_combatmg',
        'weapon_combatmg_mk2',
        'weapon_sniperrifle',
        'weapon_heavysniper',
        'weapon_heavysniper_mk2',
        'weapon_marksmanrifle',
        'weapon_marksmanrifle_mk2',
        'weapon_rpg',
        'weapon_grenadelauncher',
        'weapon_grenadelauncher_smoke',
        'weapon_minigun',
        'weapon_firework',
        'weapon_railgun',
        'weapon_hominglauncher',
        'weapon_compactlauncher',
        'weapon_rayminigun',
        'weapon_grenade',
        'weapon_bzgas',
        'weapon_molotov',
        'weapon_stickybomb',
        'weapon_proxmine',
        'weapon_pipebomb',
        'weapon_ball',
        'weapon_smokegrenade',
        'weapon_flare',
        'weapon_hazardcan',
    ];

    /**
     * Non stackable items that cannot be obtained naturally
     */
    const SingleUnusualItems = [
        'Fist',
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

        Cache::put($key, $entries, self::CacheTime);

        return $entries;
    }

    /**
     * Finds inventories containing a lot of items of the same type
     *
     * @return array
     */
    public static function findUnusualInventories(): array
    {
        $items = self::UnusualItems;
        $key = 'unusual_inventories_' . md5(json_encode($items)) . '_' . md5(json_encode(self::IgnoreItems));

        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $sql = "SELECT * FROM (SELECT `item_name`, `inventory_name`, COUNT(`item_name`) as `amount` FROM `inventories` GROUP BY (CONCAT(`item_name`, `inventory_name`))) `items` WHERE `amount` > 200 OR `item_name` IN ('" . implode("', '", $items) . "');";

        $entries = json_decode(json_encode(DB::select($sql)), true);

        $cleaned = [];
        foreach ($entries as $entry) {
            if (isset(self::IgnoreItems[$entry['item_name']]) && intval($entry['amount']) < self::IgnoreItems[$entry['item_name']]) {
                continue;
            }

            if (!isset($cleaned[$entry['inventory_name']])) {
                $cleaned[$entry['inventory_name']] = [];
            }

            $cleaned[$entry['inventory_name']][] = $entry['amount'] . 'x ' . $entry['item_name'];
        }

        $finished = [];
        foreach ($cleaned as $name => $contents) {
            $finished[] = [
                'items'     => implode("\n", $contents),
                'inventory' => $name,
            ];
        }

        Cache::put($key, $finished, self::CacheTime);

        return $entries;
    }

    /**
     * Finds item movements that have unusual amounts
     *
     * @return array
     */
    public static function findUnusualItems(): array
    {
        $key = 'unusual_item_movement_' . md5(json_encode(self::IgnoreUnusualMovement));

        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $sql = "SELECT * FROM (SELECT `identifier`, SUM(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, ' moved ', -1), ' to ', 1), 'x ', 1)) as `amount`, SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(`details`, ' moved ', -1), ' to ', 1), 'x ', -1) as `item`, `details`, CEIL(UNIX_TIMESTAMP(`timestamp`) / 600) * 600 as `time` FROM `user_logs` WHERE `action`='Item Moved' GROUP BY CONCAT(`identifier`, '|', `time`, '|', `item`) ORDER BY `time` DESC) `logs` WHERE amount > 250";

        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $logs = DB::select($sql);

        $sus = [];
        foreach ($logs as $log) {
            if (isset(self::IgnoreUnusualMovement[$log->item]) && self::IgnoreUnusualMovement[$log->item] > intval($log->amount)) {
                continue;
            }

            $sus[] = [
                'identifier' => $log->identifier,
                'details'    => 'Moved ' . number_format(intval($log->amount)) . ' of ' . $log->item . ' in the span of 10 minutes',
                'timestamp'  => date('c', intval($log->time)),
            ];
        }

        Cache::put($key, $sus, self::CacheTime);

        return $sus;
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

        Cache::put($key, $sus, self::CacheTime);

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

        $sus = self::getSaleLogEntries($sql, 20000, 'materials');

        Cache::put($key, $sus, self::CacheTime);

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
