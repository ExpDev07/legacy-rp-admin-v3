<?php

namespace App\Helpers;

use App\OPFWResponse;
use App\PanelLog;
use App\Player;
use App\Server;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Throwable;

class OPFWHelper
{
    const RetryAttempts = 2;

    /**
     * Sends a staff pm to a player
     *
     * @param string $staffLicenseIdentifier
     * @param Player $player
     * @param string $message
     * @return OPFWResponse
     */
    public static function staffPM(string $staffLicenseIdentifier, Player $player, string $message): OPFWResponse
    {
        if (!$message) {
            return new OPFWResponse(false, 'Your message cannot be empty');
        }

        $status = Player::getOnlineStatus($player->license_identifier, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(false, 'Player is offline.');
        }

        $response = self::executeRoute($status->serverIp, $status->serverIp . 'execute/staffPrivateMessage', [
            'licenseIdentifier' => $staffLicenseIdentifier,
            'targetSource'    => $status->serverId,
            'message'         => $message,
        ]);

        if ($response->status) {
            $response->message = 'Staff Message has been sent successfully.';

            PanelLog::logStaffPM($staffLicenseIdentifier, $player->license_identifier, $message);
        }

        return $response;
    }

    /**
     * Sends a staff chat message
     *
     * @param string $serverIp
     * @param string $staffLicenseIdentifier
     * @param string $message
     * @return OPFWResponse
     */
    public static function staffChat(string $serverIp, string $staffLicenseIdentifier, string $message): OPFWResponse
    {
        if (!$message) {
            return new OPFWResponse(false, 'Your message cannot be empty');
        }

        $response = self::executeRoute($serverIp, $serverIp . 'execute/staffChatMessage', [
            'licenseIdentifier' => $staffLicenseIdentifier,
            'message'         => $message,
        ]);

        if ($response->status) {
            $response->message = 'Staff Chat Message has been sent successfully.';
        }

        return $response;
    }

    /**
     * Sends a server message
     *
     * @param string $message
     * @return OPFWResponse
     */
    public static function serverAnnouncement(string $serverIp, string $message): OPFWResponse
    {
        if (!$message) {
            return new OPFWResponse(false, 'Your message cannot be empty.');
        }

        $response = self::executeRoute($serverIp, $serverIp . 'execute/announcementMessage', [
            'announcementMessage' => $message,
        ]);

        if ($response->status) {
            $response->message = 'Server Announcement has been posted successfully.';
        } else {
			$response->message = 'Failed to post server announcement.';
		}

        return $response;
    }

    /**
     * Kicks a player from the server
     *
     * @param string $staffLicenseIdentifier
     * @param string $staffPlayerName
     * @param Player $player
     * @param string $reason
     * @return OPFWResponse
     */
    public static function kickPlayer(string $staffLicenseIdentifier, string $staffPlayerName, Player $player, string $reason): OPFWResponse
    {
        $license = $player->license_identifier;

        $status = Player::getOnlineStatus($license, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(false, 'Player is offline.');
        }

        $response = self::executeRoute($status->serverIp, $status->serverIp . 'execute/kickPlayer', [
            'licenseIdentifier'         => $license,
            'reason'                  => 'You have been kicked by ' . $staffPlayerName . ' for reason `' . $reason . '`',
            'removeReconnectPriority' => false,
        ]);

        if ($response->status) {
            $response->message = 'Kicked player from the server.';

            PanelLog::logKick($staffLicenseIdentifier, $license, $reason);
        }

        return $response;
    }

    /**
     * Revives a player in the server
     *
     * @param string $staffLicenseIdentifier
     * @param string $licenseIdentifier
     * @return OPFWResponse
     */
    public static function revivePlayer(string $staffLicenseIdentifier, string $licenseIdentifier): OPFWResponse
    {
        $status = Player::getOnlineStatus($licenseIdentifier, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(false, 'Player is offline.');
        }

        $response = self::executeRoute($status->serverIp, $status->serverIp . 'execute/revivePlayer', [
            'targetSource' => $status->serverId,
        ]);

        if ($response->status) {
            $response->message = 'Revived player.';

            PanelLog::logRevive($staffLicenseIdentifier, $licenseIdentifier);
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
        $license = $player->license_identifier;

        $status = Player::getOnlineStatus($license, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(true, 'Player is offline, no refresh needed.');
        }

        $response = self::executeRoute($status->serverIp, $status->serverIp . 'execute/refreshTattoos', [
            'licenseIdentifier' => $license,
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
        $license = $player->license_identifier;

        $status = Player::getOnlineStatus($license, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(true, 'Player is offline, no refresh needed.');
        }

        $response = self::executeRoute($status->serverIp, $status->serverIp . 'execute/refreshJob', [
            'licenseIdentifier' => $license,
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
     * @param string $staffLicenseIdentifier
     * @param Player $player
     * @param string $character_id
     * @param string $message
     * @return OPFWResponse
     */
    public static function unloadCharacter(string $staffLicenseIdentifier, Player $player, string $character_id, string $message): OPFWResponse
    {
        $license = $player->license_identifier;

        $status = Player::getOnlineStatus($license, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(true, 'Player is offline, no unload needede.');
        }

        $response = self::executeRoute($status->serverIp, $status->serverIp . 'execute/unloadCharacter', [
            'licenseIdentifier' => $license,
            'characterId'     => $character_id,
            'message'         => $message,
        ]);

        if ($response->status) {
            $response->message = 'Unloaded players character.';

            PanelLog::logUnload($staffLicenseIdentifier, $license, $character_id, $message);
        }

        return $response;
    }

    /**
     * Updates someones queue position
     *
     * @param string $serverIp
     * @param string $licenseIdentifier
     * @param int $targetPosition
     * @return OPFWResponse
     */
    public static function updateQueuePosition(string $serverIp, string $licenseIdentifier, int $targetPosition): OPFWResponse
    {
        return self::executeRoute($serverIp, $serverIp . 'execute/setQueuePosition', [
            'licenseIdentifier' => $licenseIdentifier,
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
            $data = self::executeRoute($serverIp, $serverIp . 'world.json', [], 'GET', 3);

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
            $data = self::executeRoute($serverIp, $serverIp . 'queue.json', [], 'GET', 3);

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
            $data = self::executeRoute($serverIp, $serverIp . 'jobs.json', [], 'GET', 3);

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
            $data = self::executeRoute($serverIp, $serverIp . 'vehicles.json', [], 'GET', 3);

            if ($data->data) {
                CacheHelper::write($cache, $data->data, 12 * CacheHelper::HOUR);
            } else if (!$data->status) {
                CacheHelper::write($cache, [], 10);
            }

            return $data->data;
        }
    }

    /**
     * Gets the exclusiveDealership.json
     *
     * @param string $serverIp
     * @return array|null
     */
    public static function getEDMJSON(string $serverIp): ?array
    {
        $serverIp = Server::fixApiUrl($serverIp);
        $cache = 'exclusive_dealership_' . md5($serverIp);

        if (CacheHelper::exists($cache)) {
            return CacheHelper::read($cache, []);
        } else {
            $data = self::executeRoute($serverIp, $serverIp . 'exclusiveDealership.json', [], 'GET', 3);

            if ($data->data) {
                CacheHelper::write($cache, $data->data, 1 * CacheHelper::HOUR);
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
    public static function createScreenshot(string $serverIp, int $id, bool $drawHTML = true, int $lifespan = 3600): OPFWResponse
    {
        $serverIp = Server::fixApiUrl($serverIp);

        return self::executeRoute($serverIp, $serverIp . 'execute/createScreenshot', [
            'serverId' => $id,
            'lifespan' => $lifespan,
            'drawHTML' => $drawHTML
        ]);
    }

    /**
     * Creates a screen capture
     *
     * @param string $serverIp
     * @param int $id
     * @param int $duration
     * @return OPFWResponse
     */
    public static function createScreenCapture(string $serverIp, int $id, int $duration): OPFWResponse
    {
        $serverIp = Server::fixApiUrl($serverIp);

        return self::executeRoute($serverIp, $serverIp . 'execute/createScreenshot', [
            'serverId' => $id,
            'lifespan' => 60 * 60,
            'fps' => 20,
            'duration' => $duration * 1000
        ], 'POST', $duration + 15);
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
    private static function executeRoute(string $serverIp, string $route, array $data, string $requestType = 'POST', int $timeout = 10): OPFWResponse
    {
        $token = env('OP_FW_TOKEN');

        if (!$token) {
            return new OPFWResponse(false, 'Invalid OP-FW configuration.');
        }

        /*
        if (!CacheHelper::getServerStatus($serverIp)) {
            return new OPFWResponse(false, 'Server is offline (cached).');
        }
        */

        if (Str::contains($route, 'localhost')) {
            $route = str_replace('https://', 'http://', $route);
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
                if ($x+1 < self::RetryAttempts) {
                    sleep(2);
                }
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
