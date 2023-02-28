<?php

namespace App;

use App\Helpers\CacheHelper;
use App\Helpers\GeneralHelper;
use App\Helpers\OPFWHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

class Server
{
	public string $url;

    /**
     * A license_identifier->serverId map
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
        $host = str_replace('http://', '', $host);
        $host = explode('/', $host)[0];

        $name = CLUSTER . "s1";

        return preg_match('/^\d+\.\d+\.\d+\.\d+(:\d+)?$/m', $host) ? $name : explode('.', $host)[0];
    }

    /**
     * @param int $id
     * @return bool|string
     */
    public static function isServerIDValid(int $id)
    {
        $players = Player::getAllOnlinePlayers(false);

        foreach ($players as $license => $player) {
            if (intval($player['id']) === $id) {
                return $license;
            }
        }

        return false;
    }

    /**
     * Returns an associative array (licenseIdentifier -> serverId)
     *
     * @param string $serverIp
     * @param bool $useCache
     * @return array|null
     */
    public static function fetchLicenseIdentifiers(string $serverIp, bool $useCache): ?array
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
                    $assoc[$player['licenseIdentifier']] = [
                        'source'    => $player['source'],
                        'character' => $player['character'] ? $player['character']['id'] : null,
                        'flags'     => $player['flags'] ?? 0,
                        'name'      => $player['name'] ?? null
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
     * Returns all servers
     *
     * @return array
     */
    public static function getAllServers(): array
	{
		$rawServerIps = explode(',', env('OP_FW_SERVERS', ''));

		$servers = [];

		foreach ($rawServerIps as $rawServerIp) {
			$server = new Server();

			$server->url = $rawServerIp;

			$servers[] = $server;
		}

		return $servers;
	}

    /**
     * Returns all server names
     *
     * @return array
     */
    public static function getAllServerNames(): array
    {
        $rawServerIps = explode(',', env('OP_FW_SERVERS', ''));

        $serverNames = [];
        foreach ($rawServerIps as $rawServerIp) {
            $serverNames[] = Server::getServerName($rawServerIp);
        }

        return $serverNames;
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
     * Returns the first server ip found
     *
     * @return string|null
     */
    public static function getFirstServerIP(): ?string
    {
        $rawServerIps = explode(',', env('OP_FW_SERVERS', ''));

        return empty($rawServerIps) ? null : $rawServerIps[0];
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
