<?php

namespace App\Helpers;

use App\OPFWResponse;
use App\PanelLog;
use App\Player;
use App\Server;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Throwable;

class OPFWHelper
{
    const RetryAttempts = 2;

    /**
     * Sends a staff pm to a player
     *
     * @param string $staffSteamIdentifier
     * @param Player $player
     * @param string $message
     * @return OPFWResponse
     */
    public static function staffPM(string $staffSteamIdentifier, Player $player, string $message): OPFWResponse
    {
        if (!$message) {
            return new OPFWResponse(false, 'Your message cannot be empty');
        }

        $status = Player::getOnlineStatus($player->steam_identifier, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(false, 'Player is offline.');
        }

        $response = self::executeRoute($status->serverIp . 'execute/staffPrivateMessage', [
            'steamIdentifier' => $staffSteamIdentifier,
            'targetSource'    => $status->serverId,
            'message'         => $message,
        ]);

        if ($response->status) {
            $response->message = 'Staff Message has been sent successfully.';

            PanelLog::logStaffPM($staffSteamIdentifier, $player->steam_identifier, $message);
        }

        return $response;
    }

    /**
     * Kicks a player from the server
     *
     * @param string $staffSteamIdentifier
     * @param string $staffPlayerName
     * @param Player $player
     * @param string $reason
     * @return OPFWResponse
     */
    public static function kickPlayer(string $staffSteamIdentifier, string $staffPlayerName, Player $player, string $reason): OPFWResponse
    {
        $steam = $player->steam_identifier;

        $status = Player::getOnlineStatus($steam, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(false, 'Player is offline.');
        }

        $response = self::executeRoute($status->serverIp . 'execute/kickPlayer', [
            'steamIdentifier'         => $steam,
            'reason'                  => 'You have been kicked by ' . $staffPlayerName . ' for reason `' . $reason . '`',
            'removeReconnectPriority' => false,
        ]);

        if ($response->status) {
            $response->message = 'Kicked player from the server.';

            PanelLog::logKick($staffSteamIdentifier, $steam, $reason);
        }

        return $response;
    }

    /**
     * Revives a player in the server
     *
     * @param string $staffSteamIdentifier
     * @param string $steamIdentifier
     * @return OPFWResponse
     */
    public static function revivePlayer(string $staffSteamIdentifier, string $steamIdentifier): OPFWResponse
    {
        $status = Player::getOnlineStatus($steamIdentifier, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(false, 'Player is offline.');
        }

        $response = self::executeRoute($status->serverIp . 'execute/revivePlayer', [
            'targetSource' => $status->serverId,
        ]);

        if ($response->status) {
            $response->message = 'Revived player.';

            PanelLog::logRevive($staffSteamIdentifier, $steamIdentifier);
        }

        return $response;
    }

    /**
     * Updates tattoo data for a player
     *
     * @param Player $player
     * @param string $character_id
     * @return OPFWResponse
     */
    public static function updateTattoos(Player $player, string $character_id): OPFWResponse
    {
        $steam = $player->steam_identifier;

        $status = Player::getOnlineStatus($steam, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(true, 'Player is offline, no refresh needed.');
        }

        $response = self::executeRoute($status->serverIp . 'execute/refreshTattoos', [
            'steamIdentifier' => $steam,
            'characterId'     => $character_id,
        ]);

        if ($response->status) {
            $response->message = 'Updated tattoo data for player.';
        }

        return $response;
    }

    /**
     * Updates job data for a player
     *
     * @param Player $player
     * @param string $character_id
     * @return OPFWResponse
     */
    public static function updateJob(Player $player, string $character_id): OPFWResponse
    {
        $steam = $player->steam_identifier;

        $status = Player::getOnlineStatus($steam, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(true, 'Player is offline, no refresh needed.');
        }

        $response = self::executeRoute($status->serverIp . 'execute/refreshJob', [
            'steamIdentifier' => $steam,
            'characterId'     => $character_id,
        ]);

        if ($response->status) {
            $response->message = 'Updated job data for player.';
        }

        return $response;
    }

    /**
     * Unloads someone's character
     *
     * @param string $staffSteamIdentifier
     * @param Player $player
     * @param string $character_id
     * @param string $message
     * @return OPFWResponse
     */
    public static function unloadCharacter(string $staffSteamIdentifier, Player $player, string $character_id, string $message): OPFWResponse
    {
        $steam = $player->steam_identifier;

        $status = Player::getOnlineStatus($steam, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(true, 'Player is offline, no unload needede.');
        }

        $response = self::executeRoute($status->serverIp . 'execute/unloadCharacter', [
            'steamIdentifier' => $steam,
            'characterId'     => $character_id,
            'message'         => $message,
        ]);

        if ($response->status) {
            $response->message = 'Unloaded players character.';

            PanelLog::logUnload($staffSteamIdentifier, $steam, $character_id, $message);
        }

        return $response;
    }

    /**
     * Updates someones queue position
     *
     * @param string $serverIp
     * @param string $steamIdentifier
     * @param int $targetPosition
     * @return OPFWResponse
     */
    public static function updateQueuePosition(string $serverIp, string $steamIdentifier, int $targetPosition): OPFWResponse
    {
        return self::executeRoute($serverIp . 'execute/setQueuePosition', [
            'steamIdentifier' => $steamIdentifier,
            'targetPosition'  => $targetPosition,
        ], 'PATCH');
    }

    /**
     * Gets the world.json
     *
     * @param string $serverIp
     * @return array|null
     */
    public static function getWorldJSON(string $serverIp): ?array
    {
        $serverIp = Server::fixApiUrl($serverIp);
        $cache = 'world_json_' . md5($serverIp);

        if (CacheHelper::exists($cache)) {
            return CacheHelper::read($cache, []);
        } else {
            $data = self::executeRoute($serverIp . 'world.json', [], 'GET', 3);

            if ($data->data) {
                CacheHelper::write($cache, $data->data, 10);
            } else if (!$data->status) {
                CacheHelper::write($cache, [], 10);
            }

            return $data->data;
        }
    }

    /**
     * Gets the queue.json
     *
     * @param string $serverIp
     * @param bool $forceRefresh
     * @return array|null
     */
    public static function getQueueJSON(string $serverIp, bool $forceRefresh = false): ?array
    {
        $serverIp = Server::fixApiUrl($serverIp);
        $cache = 'queue_json_' . md5($serverIp);

        if (CacheHelper::exists($cache) && !$forceRefresh) {
            return CacheHelper::read($cache, []);
        } else {
            $data = self::executeRoute($serverIp . 'queue.json', [], 'GET', 3);

            if ($data->data) {
                CacheHelper::write($cache, $data->data, 3);
            } else if (!$data->status) {
                CacheHelper::write($cache, [], 3);
            }

            return $data->data;
        }
    }

    /**
     * Gets the jobs.json
     *
     * @param string $serverIp
     * @return array|null
     */
    public static function getJobsJSON(string $serverIp): ?array
    {
        $serverIp = Server::fixApiUrl($serverIp);
        $cache = 'jobs_json_' . md5($serverIp);

        if (CacheHelper::exists($cache)) {
            return CacheHelper::read($cache, []);
        } else {
            $data = self::executeRoute($serverIp . 'jobs.json', [], 'GET', 3);

            if ($data->data) {
                CacheHelper::write($cache, $data->data, 12 * CacheHelper::HOUR);
            } else if (!$data->status) {
                CacheHelper::write($cache, [], 10);
            }

            return $data->data;
        }
    }

    /**
     * Gets the vehicles.json
     *
     * @param string $serverIp
     * @return array|null
     */
    public static function getVehiclesJSON(string $serverIp): ?array
    {
        $serverIp = Server::fixApiUrl($serverIp);
        $cache = 'vehicles_json_' . md5($serverIp);

        if (CacheHelper::exists($cache)) {
            return CacheHelper::read($cache, []);
        } else {
            $data = self::executeRoute($serverIp . 'vehicles.json', [], 'GET', 3);

            if ($data->data) {
                CacheHelper::write($cache, $data->data, 12 * CacheHelper::HOUR);
            } else if (!$data->status) {
                CacheHelper::write($cache, [], 10);
            }

            return $data->data;
        }
    }

    /**
     * Creates a screenshot
     *
     * @param string $serverIp
     * @param int $id
     * @return OPFWResponse
     */
    public static function createScreenshot(string $serverIp, int $id): OPFWResponse
    {
        $serverIp = Server::fixApiUrl($serverIp);

        return self::executeRoute($serverIp . 'execute/createScreenshot', [
            'serverId' => $id,
            'lifespan' => 60 * 60,
        ]);
    }

    /**
     * Executes an op-fw route
     *
     * @param string $route
     * @param array $data
     * @param string $requestType
     * @param int $timeout
     * @return OPFWResponse
     */
    private static function executeRoute(string $route, array $data, string $requestType = 'POST', int $timeout = 10): OPFWResponse
    {
        $token = env('OP_FW_TOKEN');

        if (!$token) {
            return new OPFWResponse(false, 'Invalid OP-FW configuration.');
        }

        $result = null;

        $client = new Client(
            [
                'verify' => false,
            ]
        );
        for ($x = 0; $x < self::RetryAttempts; $x++) {
            try {
                $res = $client->request($requestType, $route, [
                    'query'   => $data,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'timeout' => $timeout,
                ]);

                $response = $res->getBody()->getContents();
            } catch (Throwable $t) {
                $response = $t->getMessage();
            }

            $log = json_encode($response);
            if (strlen($log) > 300) {
                $log = substr($log, 0, 300) . '...';
            }
            LoggingHelper::log(SessionHelper::getInstance()->getSessionKey(), 'Executed route "' . $route . '"');
            LoggingHelper::log(SessionHelper::getInstance()->getSessionKey(), 'Data: ' . json_encode($data));
            LoggingHelper::log(SessionHelper::getInstance()->getSessionKey(), 'Result: ' . $log);

            $result = self::parseResponse($response);
            if (!$result->status) {
                sleep(2);
            } else {
                return $result;
            }
        }

        return $result;
    }

    /**
     * @param string $response
     * @return OPFWResponse
     */
    public static function parseResponse(string $response): OPFWResponse
    {
        $json = json_decode($response, true);

        $code = 0;
        if ($json && isset($json['statusCode'])) {
            $code = intval($json['statusCode']);
            $category = floor(intval($json['statusCode']) / 100);

            switch (intval($json['statusCode'])) {
                case 401:
                    return new OPFWResponse(false, 'Invalid OP-FW configuration. Wrong token?');
                case 400:
                case 403:
                case 404:
                    return new OPFWResponse(false, !empty($json['message']) ? $json['message'] : 'Unknown error');
            }

            switch ($category) {
                case 2: // All 200 status codes
                    return new OPFWResponse(true, !empty($json['message']) ? 'Success: ' . $json['message'] : 'Successfully executed route', $json['data'] ?? null);
            }
        }

        return new OPFWResponse(false, 'Failed to execute route: "Invalid server response ' . $code . '"');
    }
}
