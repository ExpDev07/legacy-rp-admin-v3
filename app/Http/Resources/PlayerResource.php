<?php

namespace App\Http\Resources;

use App\Player;
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

        return [
            'id'              => $this->user_id,
            'steamIdentifier' => $this->steam_identifier,
            'playerName'      => $this->player_name,
            'playTime'        => $this->playtime,
            'lastConnection'  => $this->last_connection,
            'steamProfileUrl' => $this->getSteamProfileUrl(),
            'isStaff'         => $this->isStaff(),
            'isSuperAdmin'    => $this->isSuperAdmin(),
            'isBanned'        => $this->isBanned(),
            'warnings'        => $this->warnings()->count(),
            'ban'             => new BanResource($this->getActiveBan()),
            'status'          => $loadStatus ? Player::getOnlineStatus($this->steam_identifier, false) : null,
        ];
    }

}
