<?php

namespace App;

use App\Helpers\CacheHelper;
use App\Helpers\GeneralHelper;
use App\Helpers\OPFWHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

/**
 * @package App
 */
class Server extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'webpanel_servers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'name',
    ];

    /**
     * A steamidentifier->serverId map
     *
     * @var array
     */
    private static array $onlineMap = [];

    /**
     * Gets the API data.
     *
     * @return array
     */
    public function fetchApi(): array
    {
        $data = GeneralHelper::get(self::fixApiUrl($this->url) . 'api.json') ?? null;

        $response = OPFWHelper::parseResponse($data);

        return $response->status && $response->data ? $response->data : [];
    }

    /**
     * Gets the api url
     *
     * @param string $serverIp
     * @return string
     */
    public static function fixApiUrl(string $serverIp): string
    {
        $serverIp = Str::finish(trim($serverIp), '/');

        if (!Str::endsWith($serverIp, '/op-framework/')) {
            $serverIp .= 'op-framework/';
        }

        if (!Str::startsWith($serverIp, 'https://')) {
            $serverIp = 'https://' . $serverIp;
        }

        return $serverIp;
    }

    public static function getServerName(string $serverIp): string
    {
        $serverIp = self::fixApiUrl($serverIp);

        $host = str_replace('https://', '', $serverIp);
        $host = explode('/', $host)[0];

        $names = explode(',', env('SERVER_NAMES', ''));
        if (!empty($names)) {
            foreach ($names as $def) {
                $def = explode('=', $def);
                if (sizeof($def) === 2 && $def[0] === $host) {
                    return $def[1];
                }
            }
        }

        return preg_match('/^\d+\.\d+\.\d+\.\d+(:\d+)?$/m', $host) ? $host : explode('.', $host)[0];
    }

    /**
     * @param int $id
     * @return bool|string
     */
    public static function isServerIDValid(int $id)
    {
        $players = Player::getAllOnlinePlayers(false);

        foreach ($players as $steam => $player) {
            if (intval($player['id']) === $id) {
                return $steam;
            }
        }

        return false;
    }

    /**
     * Returns an associative array (steamIdentifier -> serverId)
     *
     * @param string $serverIp
     * @param bool $useCache
     * @return array|null
     */
    public static function fetchSteamIdentifiers(string $serverIp, bool $useCache): ?array
    {
        if (!$serverIp) {
            return [];
        }
        $cacheKey = 'server_data_' . md5($serverIp);

        if ($useCache) {
            if (CacheHelper::exists($cacheKey)) {
                return CacheHelper::read($cacheKey, []);
            }
        }

        if (!isset(self::$onlineMap[$cacheKey]) || empty(self::$onlineMap[$cacheKey])) {
            $serverIp = self::fixApiUrl($serverIp);

            try {
                $json = OPFWHelper::getWorldJSON($serverIp);

                if (!$json) {
                    return null;
                }
            } catch (Throwable $t) {
                return [];
            }

            if (isset($json['players'])) {
                $assoc = [];
                foreach ($json['players'] as $player) {
                    $assoc[$player['steamIdentifier']] = [
                        'source'    => $player['source'],
                        'character' => $player['character'] ? $player['character']['id'] : null,
                    ];
                }

                self::$onlineMap[$cacheKey] = $assoc;
            } else {
                return [];
            }
        }

        CacheHelper::write($cacheKey, self::$onlineMap[$cacheKey], 5 * CacheHelper::MINUTE);

        return self::$onlineMap[$cacheKey];
    }

    /**
     * Resolves the server api url from its name
     *
     * @param string $name
     * @return string|null
     */
    public static function getServerApiURLFromName(string $name): ?string
    {
        $rawServerIps = explode(',', env('OP_FW_SERVERS', ''));

        foreach ($rawServerIps as $rawServerIp) {
            $n = Server::getServerName($rawServerIp);
            if ($n === $name) {
                return self::fixApiUrl($rawServerIp);
            }
        }

        return null;
    }

    /**
     * Returns the first server found
     *
     * @return string|null
     */
    public static function getFirstServer(): ?string
    {
        $rawServerIps = explode(',', env('OP_FW_SERVERS', ''));

        return empty($rawServerIps) ? null : self::fixApiUrl($rawServerIps[0]);
    }

    /**
     * Collects all the /api.json data from all servers
     *
     * @return array|null
     */
    public static function collectAllApiData(): ?array
    {
        $serverIps = explode(',', env('OP_FW_SERVERS', ''));

        if (!$serverIps) {
            return [];
        }

        if (CacheHelper::exists('server_all_api')) {
            return CacheHelper::read('server_all_api', []);
        }

        $result = [];
        foreach ($serverIps as $serverIp) {
            if ($serverIp) {
                $serverIp = self::fixApiUrl($serverIp);

                try {
                    $json = GeneralHelper::get($serverIp . 'api.json') ?? [];

                    if (!$json) {
                        $result = null;
                        break;
                    } else {
                        $response = OPFWHelper::parseResponse($json);

                        if ($response->status && $response->data) {
                            $json = $response->data;

                            $result[] = [
                                'joined' => isset($json['joinedAmount']) ? intval($json['joinedAmount']) : 0,
                                'total'  => isset($json['maxClients']) ? intval($json['maxClients']) : 0,
                                'queue'  => isset($json['queueAmount']) ? intval($json['queueAmount']) : 0,
                            ];
                        }
                    }
                } catch (Throwable $t) {
                }
            }
        }

        CacheHelper::write('server_all_api', $result, 5 * CacheHelper::MINUTE);

        return $result;
    }

}
