<?php

namespace App\Http\Resources;

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
    public function toArray($request)
    {
        return [
            'id'              => $this->user_id,
            'steamIdentifier' => $this->steam_identifier,
            'playerName'      => $this->player_name,
            'playTime'        => $this->playtime,
            'lastConnection'  => $this->last_connection,
            'steamProfileUrl' => $this->getSteamProfileUrl(),
            'isStaff'         => $this->isStaff(),
            'isBanned'        => $this->isBanned(),
            'warnings'        => $this->warnings()->count(),
            'ban'             => new BanResource($this->bans()->first()),
        ];
    }

}
