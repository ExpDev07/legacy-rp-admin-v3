<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Character;
use App\Http\Requests\BanStoreRequest;
use App\Http\Requests\BanUpdateRequest;
use App\Http\Requests\CharacterUpdateRequest;
use App\Http\Resources\BanResource;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\PlayerResource;
use App\Player;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PlayerBanController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Player $player
     * @param BanStoreRequest $request
     * @return RedirectResponse
     */
    public function store(Player $player, BanStoreRequest $request)
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
     * @return RedirectResponse
     */
    public function destroy(Player $player, Ban $ban): RedirectResponse
    {
        $player->bans()->forceDelete();
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

}
