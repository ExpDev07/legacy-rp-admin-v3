<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
    ];

    /**
     * Gets the API data.
     *
     * @return array|mixed
     */
    public function fetchApi(): array
    {
        // example: https://c3s1.op-framework.com/op-framework/api.json
        return Http::get(self::fixApiUrl($this->url) . 'api.json')->json() ?? [];
    }

    /**
     * Gets the connections data.
     *
     * @return array
     */
    public function fetchConnections(): array
    {
        // example: https://c3s1.op-framework.com/op-framework/connections.json
        return Http::get(self::fixApiUrl($this->url) . 'connections.json')->json() ?? [];
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

    /**
     * Returns an associative array (steamIdentifier -> serverId)
     *
     * @param string $serverIp
     * @return array
     */
    public static function fetchSteamIdentifiers(string $serverIp): array
    {
        if (!$serverIp) {
            return [];
        }

        $serverIp = self::fixApiUrl($serverIp);

        $json = Http::timeout(3)->get($serverIp . 'connections.json')->json() ?? [];

        if ($json) {
            $assoc = [];

            foreach($json['joining']['players'] as $player) {
                $assoc[$player['steamIdentifier']] = $player['source'];
            }

            foreach($json['joined']['players'] as $player) {
                $assoc[$player['steamIdentifier']] = $player['source'];
            }

            return $assoc;
        }

        return [];
    }

}
