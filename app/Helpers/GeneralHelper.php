<?php

namespace App\Helpers;

use App\Ban;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GeneralHelper
{
    /**
     * Returns a random inspiring quote
     *
     * @return array
     */
    public static function inspiring(): array
    {
        $quote = null;
        if (Cache::store('file')->has('inspiring_quote')) {
            $quote = Cache::store('file')->get('inspiring_quote');
        }

        if (!$quote || !isset($quote['expires']) || $quote['expires'] < time()) {
            $json = json_decode(file_get_contents(__DIR__ . '/../../helpers/quotes.json'), true);

            if (!$quote || !isset($quote['expires'])) {
                $quote = [
                    'quote'   => null,
                    'author'  => null,
                    'expires' => null,
                ];
            }

            if ($json) {
                unset($quote['expires']);

                $quote = self::randomElement($json, $quote, 'quote');
                $quote['expires'] = time() + (12 * 60 * 60);

                Cache::store('file')->put('inspiring_quote', $quote);
            } else {
                $quote = [
                    'quote'  => 'Quote machine broke',
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
     * Creates a session file for authentication with the socket server
     */
    public static function updateSocketSession()
    {
        $session = SessionHelper::getInstance();
        $key = $session->getSessionKey();

        $dir = rtrim(rtrim(env('SOCKET_ROOT', ''), '/'), '\\');

        if ($dir && file_exists($dir) && is_dir($dir)) {
            $dir .= '/sessions/';

            if (!file_exists($dir)) {
                mkdir($dir);
            }

            file_put_contents($dir . '/' . $key . '.session', json_encode($session->get('user')));
        }
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
                'icon'   => $icon,
                'label'  => $label,
                'coords' => $obj,
            ];
        }

        return $result;
    }

    /**
     * Returns Ban statistics
     *
     * @return array
     */
    public static function getBanStats(): array
    {
        $key = 'ban_statistics';
        if (Cache::has($key)) {
            return Cache::get($key, []);
        }

        $stats = Ban::query()->fromSub(function ($query) {
            $query->from('user_bans')->select([
                DB::raw('FROM_UNIXTIME(`timestamp`, \'%Y-%m-%d\') AS `date`'),
            ])->orderByDesc('timestamp')->groupBy('ban_hash');
        }, 'bans')->select([
            DB::raw('COUNT(`date`) as `count`'),
            'date',
        ])->groupBy('date')->get()->toArray();

        $map = [];
        foreach ($stats as $row) {
            $map[$row['date']] = $row['count'];
        }

        $complete = [];
        for ($x = 30; $x >= 0; $x--) {
            $t = date('Y-m-d', strtotime('-' . $x . ' day'));
            if (!isset($map[$t])) {
                $complete[$t] = 0;
            } else {
                $complete[$t] = $map[$t];
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

        Cache::put($key, $data, 6 * 60 * 60);

        return $data;
    }
}
