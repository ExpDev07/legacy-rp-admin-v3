<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Http\Requests\BanStoreRequest;
use App\Http\Requests\BanUpdateRequest;
use App\Http\Resources\BanResource;
use App\Http\Resources\PlayerIndexResource;
use App\Http\Resources\PlayerResource;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PlayerBanController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->bans($request, false);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexMine(Request $request): Response
    {
        return $this->bans($request, true);
    }

    private function bans(Request $request, bool $showMine): Response
    {
        $query = Player::query();

        $query->select([
            'steam_identifier', 'player_name',
            'reason', 'timestamp', 'expire', 'creator_name', 'creator_identifier'
        ]);

        $query->leftJoin('user_bans', 'identifier', '=', 'steam_identifier');

        if ($showMine) {
            $player = $request->user()->player;

            $query->where(function ($query) use ($player) {
                $query->orWhere('creator_identifier', '=', $player->steam_identifier);
                $query->orWhereIn('creator_name', $player->player_aliases);
            });
        }

        $query
            ->whereNotNull('reason')
            ->orderByDesc('timestamp');

        $page = Paginator::resolveCurrentPage('page');
        $query->limit(15)->offset(($page - 1) * 15);

        $players = $query->get();

        return Inertia::render('Players/Bans', [
            'players' => $players->toArray(),
            'links'   => $this->getPageUrls($page),
            'page'    => $page
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param BanStoreRequest $request
     * @return RedirectResponse
     */
    public function store(Player $player, BanStoreRequest $request): RedirectResponse
    {
        if ($player->isBanned()) {
            return back()->with('error', 'Player is already banned');
        }

        // Create a unique hash to go with this player's batch of bans.
        $user = $request->user();
        $hash = Str::uuid()->toString();

        // Create ban.
        $ban = array_merge([
            'ban_hash'           => $hash,
            'creator_name'       => $user->player->player_name,
            'creator_identifier' => $user->player->steam_identifier,
        ], $request->validated());

        // Get identifiers to ban.
        $identifiers = $player->getBannableIdentifiers();

        // Go through the player's identifiers and create a ban record for each of them.
        foreach($identifiers as $identifier) {
            $b = $ban;
            $b['identifier'] = $identifier;

            $player->bans()->updateOrCreate($b);
        }

        // Create reason.
        $reason = $request->input('reason')
            ? 'I banned this person with the reason: `' . $request->input('reason') . '`'
            : 'I banned this person without a reason';

        $reason .= ($ban['expire'] ? ' for ' . $this->formatSeconds(intval($ban['expire'])) : ' indefinitely') . '.';

        // Automatically log the ban as a warning.
        $player->warnings()->create([
            'issuer_id' => $user->player->user_id,
            'message'   => $reason . ' This warning was generated automatically as a result of banning someone.',
        ]);

        return back()->with('success', 'The player has successfully been banned.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Ban $ban
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Player $player, Ban $ban, Request $request): RedirectResponse
    {
        $player->bans()->forceDelete();
        $user = $request->user();

        // Automatically log the ban update as a warning.
        $player->warnings()->create([
            'issuer_id' => $user->player->user_id,
            'message'   => 'I removed this players ban.',
        ]);

        return back()->with('success', 'The player has successfully been unbanned.');
    }

    /**
     * Display the specified resource for editing.
     *
     * @param Player $player
     * @param Ban $ban
     * @return Response
     */
    public function edit(Player $player, Ban $ban): Response
    {
        return Inertia::render('Players/Ban/Edit', [
            'player' => new PlayerResource($player),
            'ban'    => new BanResource($ban),
        ]);
    }

    /**
     * Creates a rough output like "6 days" or "1 week and 2 days"
     *
     * @param int $seconds
     * @return string
     */
    private function formatSeconds(int $seconds): string
    {
        $formatted = [];

        $months = floor($seconds / 18144000); // 30 days
        $seconds -= $months * 18144000;
        if ($months > 0) {
            $formatted[] = $months . ' month' . ($months > 1 ? 's' : '');
        }

        $weeks = floor($seconds / 604800);
        $seconds -= $weeks * 604800;
        if ($weeks > 0) {
            $formatted[] = $weeks . ' week' . ($weeks > 1 ? 's' : '');
        }

        $days = round($seconds / 86400);
        if ($days > 0 || empty($formatted)) {
            $formatted[] = $days . ' day' . ($days > 1 ? 's' : '');
        }

        $last = array_pop($formatted);
        return (empty($formatted) ? '' : implode(', ', $formatted) . ' and ') . $last;
    }

    /**
     * Updates the specified resource.
     *
     * @param Player $player
     * @param Ban $ban
     * @param BanUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(Player $player, Ban $ban, BanUpdateRequest $request): RedirectResponse
    {
        $expireBefore = $ban->getExpireTimeInSeconds() ? $this->formatSeconds($ban->getExpireTimeInSeconds()) : 'permanent';
        $expireAfter = $request->input('expire') ? $this->formatSeconds(intval($request->input('expire')) + (time() - $ban->getTimestamp())) : 'permanent';

        $bans = Ban::query()->where('ban_hash', '=', $ban->ban_hash)->get();
        foreach ($bans->values() as $b) {
            $b->update($request->validated());
        }

        $user = $request->user();
        $reason = $request->input('reason') ?: 'No reason.';

        // Automatically log the ban update as a warning.
        $player->warnings()->create([
            'issuer_id' => $user->player->user_id,
            'message'   => 'I updated this ban to be "' . $expireAfter . '" instead of "' . $expireBefore . '" and changed the reason to "' . $reason . '". ' .
                'This warning was generated automatically as a result of updating a ban.',
        ]);

        return back()->with('success', 'Ban was successfully updated, redirecting back to player page...');
    }

    public function linkedIPs(Request $request): \Illuminate\Http\Response
    {
        $steam = $request->query("steam");

        if (!$steam || !Str::startsWith($steam, 'steam:')) {
            return $this->text(400, "Invalid steam id.");
        }

        $player = Player::query()->select(['identifiers'])->where('steam_identifier', '=', $steam)->get()->first();

        if (!$player) {
            return $this->text(404, "Player not found.");
        }

        $ips = [];

        $identifiers = $player->getIdentifiers();

        foreach($identifiers as $identifier) {
            if (Str::startsWith($identifier, 'ip:')) {
                $ips[] = 'identifiers LIKE "%' . $identifier . '%"';
            }
        }

        if (empty($ips)) {
            return $this->text(404, "No IP identifiers found.");
        }

        $players = Player::query()->select(['player_name', 'steam_identifier'])->whereRaw(implode(" OR ", $ips))->get()->toArray();

        $linked = [];

        foreach($players as $found) {
            if ($found->steam_identifier !== $steam) {
                $linked[] = $found->player_name . ' (' . $found->steam_identifier . ')';
            }
        }

        return $this->text(200, "Found " . sizeof($linked) . " linked players for " . $steam . ":\n\n" . implode("\n", $linked));
    }

}
