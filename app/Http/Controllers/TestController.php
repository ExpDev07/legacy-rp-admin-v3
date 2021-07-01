<?php

namespace App\Http\Controllers;

use App\Character;
use App\Helpers\OPFWHelper;
use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    /**
     * Sets a characters tattoos to be all tattoos of a certain zone
     *
     * @param Request $request
     * @param string $token
     * @param Character $character
     * @param string $zone
     * @return Response
     */
    public function setTattoos(Request $request, string $token, Character $character, string $zone): Response
    {
        $res = self::verifyRequest($request, $character, $token);
        if ($res) {
            return $res;
        }

        $map = json_decode(file_get_contents(__DIR__ . '/../../../helpers/tattoo-map.json'), true);

        if (!$map || !is_array($map)) {
            return self::respond(false, 'Failed to load tattoo map');
        }

        $valid = PlayerCharacterController::ValidTattooZones;
        $valid[] = 'none';

        if (!$zone || !in_array($zone, $valid)) {
            return self::respond(false, 'Invalid tattoo zone');
        }

        $tattoos = json_decode($character->tattoos_data, true);
        $json = $request->query('add') ? $tattoos : [];
        if ($zone !== 'none') {
            foreach ($map as $key => $tattoo) {
                if ($tattoo['zone'] === $zone || $zone === 'all') {
                    $json[] = [
                        'overlay'    => $key,
                        'collection' => $tattoo['collection'],
                    ];
                }
            }
        }

        if (sizeof($json) > sizeof($map) * 2) {
            return self::respond(false, 'too many tattoos, max: ' . (sizeof($map) * 2) . ', current: ' . sizeof($tattoos) . ', new: ' . sizeof($json));
        }

        $character->update([
            'tattoos_data' => json_encode($json),
        ]);

        /**
         * @var $player ?Player
         */
        $player = $character->player()->first();

        $info = 'In-Game Tattoo refresh failed, user has to softnap.';
        $refresh = OPFWHelper::updateTattoos($player, $character->character_id);
        if ($refresh->status) {
            $info = 'In-Game tattoo refresh was successful too.';
        }

        return self::respond(true, 'Tattoos were set successfully. ' . $info);
    }

    /**
     * Verifies a test request
     *
     * @param Request $request
     * @param Character $character
     * @param string $token
     * @return Response|null
     */
    private static function verifyRequest(Request $request, Character $character, string $token): ?Response
    {
        if (env('DEV_API_KEY', '') !== $token || empty($token)) {
            return self::respond(false, 'Invalid op-fw token');
        }

        $staff = $request->user()->player->steam_identifier;
        if ($character->steam_identifier !== $staff) {
            return self::respond(false, 'You can only update your own characters');
        }

        return null;
    }

    /**
     * Responds with json
     *
     * @param bool $status
     * @param string $msg
     * @return Response
     */
    private static function respond(bool $status, string $msg): Response
    {
        return (new Response([
            'status'  => $status,
            'message' => $msg,
        ], 200))->header('Content-Type', 'application/json');
    }
}
