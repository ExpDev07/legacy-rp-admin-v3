<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeneralHelper
{
    const DefaultDannies = [
        -1667301416 => ["headBlendData"=>["skinSecondId"=>0,"shapeSecondId"=>0,"shapeMix"=>0.0,"isParent"=>false,"skinMix"=>0.0,"thirdMix"=>0.0,"shapeThirdId"=>0,"shapeFirstId"=>0,"skinFirstId"=>0,"skinThirdId"=>0],"headOverlay"=>[],"hairColor"=>["colorId"=>-1,"highlightColorId"=>-1],"components"=>["1"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"2"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"3"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"4"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"5"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"6"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"7"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"8"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>240],"9"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"10"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"11"=>["drawableId"=>6,"paletteId"=>0,"textureId"=>1],"0"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0]],"props"=>["1"=>["textureId"=>-1,"drawableId"=>-1],"2"=>["textureId"=>-1,"drawableId"=>-1],"0"=>["textureId"=>-1,"drawableId"=>-1],"7"=>["textureId"=>-1,"drawableId"=>-1],"6"=>["textureId"=>-1,"drawableId"=>-1]],"faceFeatures"=>["1"=>0.0,"2"=>0.0,"3"=>0.0,"4"=>0.0,"5"=>0.0,"6"=>0.0,"7"=>0.0,"8"=>0.0,"9"=>0.0,"10"=>0.0,"11"=>0.0,"12"=>0.0,"13"=>0.0,"14"=>0.0,"15"=>0.0,"16"=>0.0,"17"=>0.0,"18"=>0.0,"19"=>0.0,"0"=>0.0],"eyeColor"=>-1],
        1885233650 => ["headBlendData"=>["skinSecondId"=>0,"shapeSecondId"=>0,"shapeMix"=>0.0,"isParent"=>false,"skinMix"=>0.0,"thirdMix"=>0.0,"shapeThirdId"=>0,"shapeFirstId"=>0,"skinFirstId"=>0,"skinThirdId"=>0],"headOverlay"=>[],"hairColor"=>["colorId"=>-1,"highlightColorId"=>-1],"components"=>["1"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"2"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"3"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"4"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"5"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"6"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"7"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"8"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>240],"9"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"10"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0],"11"=>["drawableId"=>6,"paletteId"=>0,"textureId"=>1],"0"=>["drawableId"=>0,"paletteId"=>0,"textureId"=>0]],"props"=>["1"=>["textureId"=>-1,"drawableId"=>-1],"2"=>["textureId"=>-1,"drawableId"=>-1],"0"=>["textureId"=>-1,"drawableId"=>-1],"7"=>["textureId"=>-1,"drawableId"=>-1],"6"=>["textureId"=>-1,"drawableId"=>-1]],"faceFeatures"=>["1"=>0.0,"2"=>0.0,"3"=>0.0,"4"=>0.0,"5"=>0.0,"6"=>0.0,"7"=>0.0,"8"=>0.0,"9"=>0.0,"10"=>0.0,"11"=>0.0,"12"=>0.0,"13"=>0.0,"14"=>0.0,"15"=>0.0,"16"=>0.0,"17"=>0.0,"18"=>0.0,"19"=>0.0,"0"=>0.0],"eyeColor"=>-1]
    ];

    const PedComponents = [
        4, // Pants
        6, // Shoes
        7, // Necklace and Ties
        10, // Decals
        11, // Shirts
        3, // Arms
        1, // Masks
    ];

    const PedOverlays = [
        2, // Eyebrows
        4, // Makeup
        5, // Blush
        8, // Lipstick
        9, // Moles and Freckles
    ];

    const PedProps = [
        0, // Hats
        6, // Left Wrist
    ];

    /**
     * @var array
     */
    private static array $GETCache = [];

    /**
     * @var null|array
     */
    private static ?array $rootCache = null;

    public static function getRootUsers(): array
    {
        if (!self::$rootCache) {
            $config = __DIR__ . '/../../envs/root-config.json';

            self::$rootCache = [];

            if (file_exists($config)) {
                $json = json_decode(file_get_contents(__DIR__ . '/../../envs/root-config.json'), true);

                if (is_array($json)) {
                    foreach ($json as $user) {
                        if (!empty($user['license'])) {
                            self::$rootCache[] = $user['license'];
                        }
                    }

                    self::$rootCache = array_values(array_unique(self::$rootCache));
                }
            }
        }

        return self::$rootCache;
    }

    public static function isUserRoot(string $license_identifier): bool
    {
        $users = self::getRootUsers();

        return in_array($license_identifier, $users);
    }

    public static function ipInfo(string $ip): ?array
    {
        DB::table('panel_ip_infos')->where('last_crawled', '<', time() - CacheHelper::DAY * 5)->delete();

        $info = DB::table('panel_ip_infos')->where('ip', '=', $ip)->get()->first();

        if (!$info) {
            $info = json_decode(GeneralHelper::get("http://ip-api.com/json/" . $ip . "?fields=status,message,country,isp,proxy,hosting"), true) ?? [];
            if (!$info || $info['status'] !== 'success') {
                return null;
            }

            unset($info['status']);

            $info = [
                'ip' => $ip,
                'country' => $info['country'] ?? 'N/A',
                'isp' => $info['isp'] ?? 'N/A',
                'proxy' => $info['proxy'] ? 1 : 0,
                'hosting' => $info['hosting'] ? 1 : 0,
                'last_crawled' => time()
            ];

            DB::table('panel_ip_infos')->insert($info);
        } else {
            $info = json_decode(json_encode($info), true);
        }

        return $info;
    }

	public static function dannyPercentageCreationTime($creationTime)
	{
		if (!$creationTime) {
			return false;
		}

		$creationTime = max(0, $creationTime - 15);

		$percentage = 100 - ($creationTime / (15 * 60));

		return min(100, max(0, $percentage));
	}

    public static function isDefaultDanny($modelHash, $modelData)
    {
        $modelData = is_string($modelData) ? json_decode($modelData, true) : $modelData;

        if (!$modelData || !isset($modelData["headOverlay"]) || !isset($modelData["props"]) || !isset($modelData["components"]) || !isset($modelData["headBlendData"])) {
            return false;
        }

        if ($modelHash !== 1885233650 && $modelHash !== -1667301416) {
            return false;
        }

        $default = self::DefaultDannies[$modelHash];

        $changed = 0;

        foreach($modelData["components"] as $componentId => $component) {
            if (!in_array(intval($componentId), self::PedComponents)) continue;

            $defaultValue = $default["components"][$componentId] ?? null;

            if ($component["drawableId"] !== $defaultValue["drawableId"]) {
                $changed++;
            }
        }

        foreach($modelData["headOverlay"] as $overlayId => $overlay) {
            if (!in_array(intval($overlayId), self::PedOverlays)) continue;

            if ($overlay["overlayOpacity"] >= 0.3 && $overlay["overlayValue"] !== 255) {
                $changed++;
            }
        }

        foreach($modelData["props"] as $propId => $prop) {
            if (!in_array(intval($propId), self::PedProps)) continue;

            $defaultValue = $default["props"][$propId] ?? null;

            if ($prop["drawableId"] !== $defaultValue["drawableId"]) {
                $changed++;
            }
        }

        $total = sizeof(self::PedComponents) + sizeof(self::PedOverlays) + sizeof(self::PedProps);
        $percentage = 1 - ($changed / $total);

        $defaultHeadBlend = $default["headBlendData"];

        $isDefaultSkin1 = $modelData["headBlendData"]["skinFirstId"] === $defaultHeadBlend["skinFirstId"] ? 0.05 : 0;
        $isDefaultSkin2 = $modelData["headBlendData"]["skinSecondId"] === $defaultHeadBlend["skinSecondId"] ? 0.05 : 0;

        $isDefaultShape1 = $modelData["headBlendData"]["shapeFirstId"] === $defaultHeadBlend["shapeFirstId"] ? 0.05 : 0;
        $isDefaultShape2 = $modelData["headBlendData"]["shapeSecondId"] === $defaultHeadBlend["shapeSecondId"] ? 0.05 : 0;

        return ($percentage * 0.8) + $isDefaultSkin1 + $isDefaultSkin2 + $isDefaultShape1 + $isDefaultShape2;
    }

    /**
     * Returns a random inspiring quote
     *
     * @return array
     */
    public static function inspiring(): array
    {
        $key = 'inspiring_quote';
        $quote = null;
        if (CacheHelper::exists($key)) {
            $quote = CacheHelper::read($key, []);
        }

        if (!$quote || !isset($quote['expires']) || $quote['expires'] < time()) {
            $json = json_decode(file_get_contents(__DIR__ . '/../../helpers/quotes.json'), true);

            if (!$quote || !isset($quote['expires'])) {
                $quote = [
                    'quote' => null,
                    'author' => null,
                    'expires' => null,
                ];
            }

            if ($json) {
                unset($quote['expires']);

                $quote = self::randomElement($json, $quote, 'quote');
                $quote['expires'] = time() + (12 * 60 * 60);

                CacheHelper::write($key, $quote);
            } else {
                $quote = [
                    'quote' => 'Quote machine broke',
                    'author' => 'Twoot',
                ];
            }
        }

        return $quote;
    }

    /**
     * Tries to pick a random element from an array without choosing the last one
     *
     * @param array $array
     * @param array $lastElement
     * @param string $compareKey
     * @return array
     */
    public static function randomElement(array $array, array $lastElement, string $compareKey): array
    {
        $attempts = 0;

        $newElement = $array[array_rand($array)];
        while ($newElement[$compareKey] === $lastElement[$compareKey] && $attempts < 1000) {
            $newElement = $array[array_rand($array)];
            $attempts++;
        }

        return $newElement;
    }

    /**
     * Parses a .map file
     *
     * @param string $file
     * @return array|null
     */
    public static function parseMapFile(string $file): ?array
    {
        if (!file_exists($file)) {
            return null;
        }

        $contents = str_replace("\r\n", "\n", file_get_contents($file));
        if (!$contents) {
            return null;
        }

        $lines = explode("\n", $contents);
        $result = [];
        foreach ($lines as $line) {
            $re = '/^({.+?}) (.+?) (.+)$/m';
            preg_match($re, $line, $matches);

            if (sizeof($matches) < 4) {
                continue;
            }

            $icon = $matches[2];
            $obj = $matches[1];
            $label = $matches[3];

            $result[] = [
                'icon' => $icon,
                'label' => $label,
                'coords' => $obj,
            ];
        }

        return $result;
    }

    /**
     * Simple GET request
     *
     * @param string $url
     * @return string
     */
    public static function get(string $url, int $timeout = 3, int $connectTimeout = 1): string
    {
        if (isset(self::$GETCache[$url])) {
            LoggingHelper::quickLog("Returning cached request '" . $url . "'");
            return self::$GETCache[$url];
        }

        $start = round(microtime(true) * 1000);

        try {
            $client = new Client(
                [
                    'verify' => false,
                ]
            );

            $res = $client->request('GET', $url, [
                'timeout' => $timeout,
                'connect_timeout' => $connectTimeout,
            ]);

            $body = $res->getBody()->getContents();
        } catch (\Throwable $t) {
            $taken = round(microtime(true) * 1000) - $start;
            LoggingHelper::quickLog("Request error '" . $url . "' in " . $taken . "ms");
            LoggingHelper::quickLog(get_class($t) . ': ' . $t->getMessage());

            self::$GETCache[$url] = '';
            return '';
        }

        $taken = round(microtime(true) * 1000) - $start;
        LoggingHelper::quickLog("Completed request '" . $url . "' in " . $taken . "ms");

        self::$GETCache[$url] = $body;

        return $body;
    }

    public static function getCluster(): ?string
    {
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $_SERVER['HTTP_HOST'] = trim(explode(':', $_SERVER['HTTP_HOST'])[0]);

        $cluster = explode('.', $_SERVER['HTTP_HOST'])[0];

        if (empty($cluster)) {
            return null;
        } else if ($cluster === 'localhost') {
            return 'c1';
        } else {
            return $cluster;
        }
    }
}
