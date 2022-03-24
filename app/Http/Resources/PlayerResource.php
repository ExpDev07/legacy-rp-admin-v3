<?php

namespace App\Http\Resources;

use App\Player;
use App\Warning;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $path = explode('/', $request->path());
        $loadStatus = sizeof($path) === 2 && $path[0] = 'players';

        $status = $loadStatus ? Player::getOnlineStatus($this->steam_identifier, false) : null;

        $identifiers = json_decode($this->player_aliases, true);

        return [
            'id'              => $this->user_id,
            'avatar'          => $this->avatar,
            'discord'         => $this->getDiscordID(),
            'steamIdentifier' => $this->steam_identifier,
            'steam36'         => base_convert(str_replace('steam:', '', $this->steam_identifier), 16, 36),
            'playerName'      => $this->player_name,
            'playTime'        => $this->playtime,
            'lastConnection'  => $this->last_connection,
            'steamProfileUrl' => $this->getSteamProfileUrl(),
            'isTrusted'       => $this->is_trusted,
            'isDebugger'      => $this->isDebugger(),
            'isPanelTrusted'  => $this->isPanelTrusted(),
            'isStaff'         => $this->isStaff(),
            'isSuperAdmin'    => $this->isSuperAdmin(),
            'isRoot'          => $this->isRoot(),
            'isBanned'        => $this->isBanned(),
            'warnings'        => $this->warnings()->whereIn('warning_type', [Warning::TypeStrike, Warning::TypeWarning])->count(),
            'ban'             => new BanResource($this->getActiveBan()),
            'status'          => $status,
            'fakeName'        => $status ? $status->fakeName : false,
            'playerAliases'   => $identifiers ? array_values(array_filter($identifiers, function($e) {
                return $e !== $this->player_name;
            })) : []
        ];
    }

}
