<?php

namespace App\Helpers;

use App\Player;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class CacheHelper
{
    const MINUTE = 60;
    const HOUR   = self::MINUTE * 60;
    const DAY    = self::HOUR * 24;
    const WEEK   = self::DAY * 7;
    const MONTH  = self::DAY * 30;

    /**
     * Loads a map for steamIdentifier->PlayerName
     *
     * @param array $identifiers
     * @return array
     */
    public static function loadSteamPlayerNameMap(array $identifiers): array
    {
        $map = self::read('steam_player_map', []);
        $missingIdentifiers = array_values(array_filter($identifiers, function ($identifier) use ($map) {
            return !isset($map[$identifier]);
        }));

        if (!empty($missingIdentifiers)) {
            $players = Player::query()->whereIn('steam_identifier', $identifiers)->select([
                'steam_identifier', 'player_name',
            ])->get();
            foreach ($players as $player) {
                $map[$player->steam_identifier] = $player->player_name;
            }

            self::write('steam_player_map', $map, 12 * self::HOUR);
        }

        $filtered = [];
        foreach ($identifiers as $identifier) {
            $filtered[$identifier] = $map[$identifier];
        }

        return $filtered;
    }

    /**
     * Write something to the cache
     *
     * @param string $key
     * @param $data
     * @param int|null $ttl
     */
    public static function write(string $key, $data, ?int $ttl = null)
    {
        if (CLUSTER && !Str::startsWith($key, CLUSTER)) {
            $key = CLUSTER . $key;
        }

        try {
            Cache::store('file')->set($key, $data, $ttl);
        } catch (InvalidArgumentException $e) {
        }
    }

    /**
     * Read something from the cache
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function read(string $key, $default = null)
    {
        if (CLUSTER && !Str::startsWith($key, CLUSTER)) {
            $key = CLUSTER . $key;
        }

        try {
            return Cache::store('file')->get($key, $default);
        } catch (InvalidArgumentException $e) {
        }

        return $default;
    }

    /**
     * Check if something exists in cache
     *
     * @param string $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
        if (CLUSTER && !Str::startsWith($key, CLUSTER)) {
            $key = CLUSTER . $key;
        }

        try {
            return Cache::store('file')->has($key);
        } catch (InvalidArgumentException $e) {
        }

        return false;
    }
}
