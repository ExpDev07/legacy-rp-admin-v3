<?php

namespace App\Helpers;

use App\Http\Requests\KickStoreRequest;
use App\Http\Requests\StaffPMStoreRequest;
use App\OPFWResponse;
use App\PanelLog;
use App\Player;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use MongoDB\Driver\Session;

class OPFWHelper
{
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

        $response = self::executeRoute($status->serverIp . 'execute/staffMessage', [
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
     * @param Player $player
     * @param string $reason
     * @return OPFWResponse
     */
    public static function kickPlayer(string $staffSteamIdentifier, Player $player, string $reason): OPFWResponse
    {
        $steam = $player->steam_identifier;

        $status = Player::getOnlineStatus($player->steam_identifier, false);
        if (!$status->isOnline()) {
            return new OPFWResponse(false, 'Player is offline.');
        }

        $response = self::executeRoute($status->serverIp . 'execute/kickPlayer', [
            'steamIdentifier'         => $steam,
            'reason'                  => $reason,
            'removeReconnectPriority' => false,
        ]);

        if ($response->status) {
            $response->message = 'Kicked player from the server.';

            PanelLog::logKick($staffSteamIdentifier, $player->steam_identifier, $reason);
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

        $status = Player::getOnlineStatus($player->steam_identifier, false);
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
     * Executes an op-fw route
     *
     * @param string $route
     * @param array $data
     * @return OPFWResponse
     */
    private static function executeRoute(string $route, array $data): OPFWResponse
    {
        $token = env('OP_FW_TOKEN');

        if (!$token) {
            return new OPFWResponse(false, 'Invalid OP-FW configuration.');
        }

        $client = new Client();
        $res = $client->request('POST', $route, [
            'query'   => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $response = $res->getBody()->getContents();

        LoggingHelper::log(SessionHelper::getInstance()->getSessionKey(), 'Executed route "' . $route . '"');
        LoggingHelper::log(SessionHelper::getInstance()->getSessionKey(), 'Data: ' . json_encode($data));
        LoggingHelper::log(SessionHelper::getInstance()->getSessionKey(), 'Result: ' . json_encode($response));

        return self::parseResponse($response);
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
                    return new OPFWResponse(true, !empty($json['message']) ? 'Success: ' . $json['message'] : 'Successfully executed route', isset($json['data']) ? $json['data'] : null);
            }
        }

        return new OPFWResponse(false, 'Failed to execute route: "Invalid server response ' . $code . '"');
    }
}
