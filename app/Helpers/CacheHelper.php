<?php

namespace App\Helpers;

use App\Log;
use App\Player;
use App\Server;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class CacheHelper
{
    const MINUTE = 60;
    const HOUR = self::MINUTE * 60;
    const DAY = self::HOUR * 24;
    const WEEK = self::DAY * 7;
    const MONTH = self::DAY * 30;

    /**
     * Loads a map for licenseIdentifier->PlayerName
     *
     * @param array $identifiers
     * @return array
     */
    public static function loadLicensePlayerNameMap(array $identifiers): array
    {
        $map = self::read('license_player_map', []);
        $missingIdentifiers = array_values(array_filter($identifiers, function ($identifier) use ($map) {
            return !isset($map[$identifier]);
        }));

        if (!empty($missingIdentifiers)) {
            $players = Player::query()->whereIn('license_identifier', $identifiers)->select([
                'license_identifier', 'player_name',
            ])->get();
            foreach ($players as $player) {
                $map[$player->license_identifier] = $player->player_name;
            }

            self::write('license_player_map', $map, 12 * self::HOUR);
        }

        $filtered = [];
        foreach ($identifiers as $identifier) {
            if (!isset($map[$identifier])) {
                continue;
            }
            $filtered[$identifier] = $map[$identifier];
        }

        return $filtered;
    }

    /**
     * Array of possible log actions
     *
     * @param bool $forceRefresh
     * @return array
     */
    public static function getLogActions(bool $forceRefresh = false): array
    {
        $actions = self::read('actions', null);

        if (!$actions || $forceRefresh) {
            $actions = Log::query()->selectRaw('action, COUNT(action) as count')->groupBy('action')->get()->toArray();

            self::write('actions', $actions, self::HOUR * 2);
        }

        return $actions;
    }

    /**
     * boolean with the server status
     *
     * @return array
     */
    public static function getServerStatus(string $serverIp, bool $doRefresh = false): bool
    {
        $serverIp = Server::fixApiUrl($serverIp);

        if (!$doRefresh) {
            return self::read("status__" . $serverIp, true);
        }

        $data = json_decode(GeneralHelper::get($serverIp . "api.json", 5, 3), true);
        if (!$data) {
            echo "Trying again...";

            $data = json_decode(GeneralHelper::get($serverIp . "api.json", 10, 5), true);
            if (!$data) {
                echo "Trying again...";

                $data = json_decode(GeneralHelper::get($serverIp . "api.json", 15, 10), true);
            }
        }

        $status = false;

        if ($data && $data["statusCode"] === 200 && $data["data"] && $data["data"]["serverVersion"]) {
            $status = true;
        }

        self::write("status__" . $serverIp, $status, self::MINUTE * 5);

        return $status;
    }

    /**
     * Get model -> display name map
     *
     * @return array
     */
    public static function getVehicleMap(): array
    {
        $map = self::read('vehicle_map', null);
        if (!$map) {
            $map = json_decode(GeneralHelper::get('https://raw.githubusercontent.com/twooot/legacyrp-admin-panel-sockets/master/vehicles.json'), true) ?? [];

            if ($map) {
                $list = [];

                foreach ($map['data'] as $model) {
                    $list[] = $model;
                }

                $map = $list;
            }

            self::write('vehicle_map', $map, self::DAY);
        }

        return $map;
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
