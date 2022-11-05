<?php

namespace App\Http\Controllers;

use App\Helpers\LoggingHelper;
use App\Helpers\OPFWHelper;
use App\Helpers\PermissionHelper;
use App\Player;
use App\Screenshot;
use App\Server;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\DB;

class PlayerRouteController extends Controller
{
    const AllowedIdentifiers = [
        'steam',
        'discord',
        'fivem',
        'license',
        'license2',
        'live',
        'xbl',
    ];

    /**
     * Kick a player from the game
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function kick(Player $player, Request $request): RedirectResponse
    {
        if (empty(trim($request->input('reason')))) {
            return back()->with('error', 'Reason cannot be empty');
        }

        $user = $request->user();
        $reason = $request->input('reason') ?: 'You were kicked by ' . $user->player->player_name;

        return OPFWHelper::kickPlayer($user->player->steam_identifier, $user->player->player_name, $player, $reason)->redirect();
    }

    /**
     * Send a staffPM to a player
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function staffPM(Player $player, Request $request): RedirectResponse
    {
        $user = $request->user();
        $message = trim($request->input('message'));

        if (empty($message)) {
            return back()->with('error', 'Message cannot be empty');
        }

        return OPFWHelper::staffPM($user->player->steam_identifier, $player, $message)->redirect();
    }

    /**
     * Unload someones character
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function unloadCharacter(Player $player, Request $request): RedirectResponse
    {
        $user = $request->user();
        $character = trim($request->input('character'));

        if (empty($character)) {
            return back()->with('error', 'Character ID cannot be empty');
        }

        $message = trim($request->input('message'));

        return OPFWHelper::unloadCharacter($user->player->steam_identifier, $player, $character, $message)->redirect();
    }

    /**
     * Returns all linked accounts
     *
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function linkedAccounts(Player $player, Request $request): Response
    {
        $identifiers = $player->getIdentifiers();
        $linked = [
            'total'  => 0,
            'linked' => [],
        ];

        foreach ($identifiers as $identifier) {
            if (Str::startsWith($identifier, "ip:")) {
                continue;
            }

            if (!isset($linked['linked'][$identifier])) {
                $linked['linked'][$identifier] = [
                    'label'    => Player::getIdentifierLabel($identifier) ?? 'Unknown Identifier',
                    'accounts' => [],
                ];
            }

            $accounts = Player::query()
                ->where('identifiers', 'LIKE', '%' . $identifier . '%')
                ->where('steam_identifier', '!=', $player->steam_identifier)
                ->select(['steam_identifier', 'player_name'])
                ->get()->toArray();
            $linked['linked'][$identifier]['accounts'] = $accounts;

            $linked['total'] += sizeof($accounts);
        }

        return (new Response([
            'status' => true,
            'data'   => $linked,
        ], 200))->header('Content-Type', 'application/json');
    }

    /**
     * Returns all discord accounts
     *
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function discordAccounts(Player $player, Request $request): Response
    {
        return (new Response([
            'status' => true,
            'data'   => $player->getDiscordInfo(),
        ], 200))->header('Content-Type', 'application/json');
    }

    /**
     * Returns all anti cheat information.
     *
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function antiCheat(Player $player, Request $request): Response
    {
        $events = DB::table("anti_cheat_events")->where('steam_identifier', $player->steam_identifier)->orderByDesc("timestamp")->limit(200)->get()->toArray();

        return (new Response([
            'status' => true,
            'data'   => array_map(function($entry) {
                $entry->metadata = json_decode($entry->metadata);

                return $entry;
            }, $events),
        ], 200))->header('Content-Type', 'application/json');
    }

    /**
     * Returns all screenshots.
     *
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function screenshots(Player $player, Request $request): Response
    {
        return (new Response([
            'status' => true,
            'data'   => Screenshot::getAllScreenshotsForPlayer($player->steam_identifier),
        ], 200))->header('Content-Type', 'application/json');
    }

    /**
     * Returns all panel logs.
     *
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function panelLogs(Player $player, Request $request): Response
    {
        return (new Response([
            'status' => true,
            'data'   => PanelLogResource::collection($player->panelLogs()->orderByDesc('timestamp')->limit(10)->get()),
        ], 200))->header('Content-Type', 'application/json');
    }

    /**
     * Revives the player
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function revivePlayer(Player $player, Request $request): RedirectResponse
    {
        $user = $request->user();

        return OPFWHelper::revivePlayer($user->player->steam_identifier, $player->steam_identifier)->redirect();
    }

    /**
     * Removes a certain identifier
     *
     * @param Player $player
     * @param string $identifier
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeIdentifier(Player $player, string $identifier, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can remove identifiers.');
        }

        $identifiers = $player->getIdentifiers();

        if (!in_array($identifier, $identifiers)) {
            return back()->with('error', 'That identifier doesn\'t belong to the player.');
        }

        $type = explode(':', $identifier)[0];
        if (!in_array($type, self::AllowedIdentifiers)) {
            return back()->with('error', 'You cannot remove the identifier of type "' . $type . '".');
        }

        $filtered = array_values(array_filter($identifiers, function ($id) use ($identifier) {
            return $id !== $identifier;
        }));

        $player->update([
            'identifiers' => $filtered,
        ]);

        return back()->with('success', 'Identifier has been removed successfully.');
    }

    /**
     * Sets the trusted panel permission
     *
     * @param Player $player
     * @param int $status
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateTrustedPanelStatus(Player $player, int $status, Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user->player->is_super_admin) {
            return back()->with('error', 'Only super admins can update this permission.');
        }

        $status = $status ? 1 : 0;

        if (!$player->isStaff()) {
            return back()->with('error', 'You cannot modify this permission on non-staff players.');
        }

        $player->update([
            'is_panel_trusted' => $status,
        ]);

        return back()->with('success', 'Panel trusted permission has been updated successfully.');
    }

    /**
     * Sets the soft ban status
     *
     * @param Player $player
     * @param int $status
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateSoftBanStatus(Player $player, int $status, Request $request): RedirectResponse
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_SOFT_BAN)) {
            return back()->with('error', 'You dont have permissions to do this.');
        }

        $status = $status ? 1 : 0;

        $player->update([
            'is_soft_banned' => $status,
        ]);

        return back()->with('success', 'Soft ban status has been updated successfully.');
    }

    /**
     * Sets the tag
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateTag(Player $player, Request $request): RedirectResponse
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_EDIT_TAG)) {
            return back()->with('error', 'You dont have permissions to do this.');
        }

        $tag = $request->input('tag') ? trim($request->input('tag')) : null;

        $player->update([
            'panel_tag' => $tag,
        ]);

        Player::resolveTags(true);

        return back()->with('success', 'Tag has been updated successfully.');
    }

    /**
     * Updates the role
     *
     * @param Player $player
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateRole(Player $player, Request $request): RedirectResponse
    {
        if (!env('ALLOW_ROLE_EDITING', false) || !$this->isSuperAdmin($request)) {
            return back()->with('error', 'You dont have permissions to do this.');
        }

        $role = $request->input('role') ? trim($request->input('role')) : null;

        $data = [
            'is_trusted' => 0,
            'is_staff' => 0,
            'is_senior_staff' => 0
        ];

        if ($role === 'seniorStaff') {
            $data['is_senior_staff'] = 1;
        } else if ($role === 'staff') {
            $data['is_staff'] = 1;
        } else if ($role === 'trusted') {
            $data['is_trusted'] = 1;
        }

        $player->update($data);

        return back()->with('success', 'Role has been updated successfully.');
    }

    /**
     * Takes a screenshot
     *
     * @param string $server
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function screenshot(string $server, int $id, Request $request): Response
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_SCREENSHOT)) {
            return self::json(false, null, 'Only trusted Panel users can use screenshot functionality');
        }

        $api = Server::getServerApiURLFromName($server);
        if (!$api) {
            return self::json(false, null, 'Invalid server');
        }

        $steam = Server::isServerIDValid($id);
        if (!$steam) {
            return self::json(false, null, 'Invalid server id (User is offline?)');
        }

        $data = OPFWHelper::createScreenshot($api, $id);

        if ($data->status) {
            return self::json(true, [
                'url'   => $data->data['screenshotURL'],
                'steam' => $steam,
            ]);
        } else {
            return self::json(false, null, 'Failed to create screenshot');
        }
    }

    /**
     * Takes a screen capture
     *
     * @param string $server
     * @param int $id
     * @param int $duration
     * @param Request $request
     * @return Response
     */
    public function capture(string $server, int $id, int $duration, Request $request): Response
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_SCREENSHOT)) {
            return self::json(false, null, 'Only trusted Panel users can use screenshot functionality');
        }

        $api = Server::getServerApiURLFromName($server);
        if (!$api) {
            return self::json(false, null, 'Invalid server');
        }

        $steam = Server::isServerIDValid($id);
        if (!$steam) {
            return self::json(false, null, 'Invalid server id (User is offline?)');
        }

        if ($duration < 3 || $duration > 30) {
            return self::json(false, null, 'Invalid duration');
        }

        $data = OPFWHelper::createScreenCapture($api, $id, $duration);

        if ($data->status) {
            return self::json(true, [
                'url'   => $data->data['screenshotURL'],
                'steam' => $steam,
            ]);
        } else {
            return self::json(false, null, 'Failed to create screen capture');
        }
    }

    /**
     * @param Player $player
     * @param Request $request
     * @return Response
     */
    public function attachScreenshot(Player $player, Request $request): Response
    {
        if (!PermissionHelper::hasPermission($request, PermissionHelper::PERM_SCREENSHOT)) {
            return self::json(false, null, 'Only trusted Panel users can use screenshot functionality');
        }

        $screenshotUrl = trim($request->input('url')) ?? '';

        $re = '/^https:\/\/api\.op-framework\.com\/files\/public\/\d{1,2}-\d{1,2}-\d{4}-\w+\.jpg$/m';
        if (!preg_match($re, $screenshotUrl)) {
            return self::json(false, null, 'Invalid screenshot url');
        }

        $note = trim($request->input('note')) ?? '';
        if (strlen($note) > 500) {
            return self::json(false, null, 'Note cannot be longer than 500 characters');
        }

        $fileName = md5($screenshotUrl) . '.jpg';

        $exists = !!Screenshot::query()->where('filename', '=', $fileName)->first();
        if ($exists) {
            return self::json(false, null, 'Screenshot already exists');
        }

        $dir = storage_path('screenshots');

        if (!file_exists($dir)) {
            mkdir($dir);
        }

        $screenshot = null;
        try {
            $client = new Client(
                [
                    'verify' => false,
                ]
            );

            $res = $client->request('GET', $screenshotUrl);

            $screenshot = $res->getBody()->getContents();
        } catch (\Throwable $t) {
            LoggingHelper::quickLog("Failed to download screenshot from " . $screenshotUrl);
            LoggingHelper::quickLog(get_class($t) . ': ' . $t->getMessage());
        }

        if (!$screenshot) {
            return self::json(false, null, 'Failed to download screenshot');
        }

        if (!file_put_contents($dir . '/' . $fileName, $screenshot)) {
            return self::json(false, null, 'Failed to store screenshot');
        }

        Screenshot::query()->create([
            'steam_identifier' => $player->steam_identifier,
            'filename'         => $fileName,
            'note'             => $note ?? '',
            'created_at'       => time(),
        ]);

        return self::json(true, 'Screenshot was attached to players profile successfully');
    }

    /**
     * @param string $screenshot
     * @return BinaryFileResponse
     */
    public function exportScreenshot(string $screenshot): BinaryFileResponse
    {
        if (!preg_match('/^\w{32}\.jpg$/m', $screenshot)) {
            abort(400);
        }

        $path = storage_path('screenshots') . '/' . $screenshot;

        return response()->file($path, [
            'Content-type: image/jpeg'
        ]);
    }

}
