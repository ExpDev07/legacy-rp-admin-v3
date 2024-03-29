<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class GeneralHelper
{
    private static $GETCache = [];

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
     * Simple GET request
     *
     * @param string $url
     * @return string
     */
    public static function get(string $url): string
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
                'timeout'         => 3,
                'connect_timeout' => 1,
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
}
