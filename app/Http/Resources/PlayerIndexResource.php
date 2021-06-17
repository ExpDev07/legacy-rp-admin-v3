<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerIndexResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'steamIdentifier' => $this->steam_identifier,
            'playerName'      => $this->player_name,
            'playTime'        => $this->playtime,
            'warnings'        => $this->warning_count,
            'isBanned'        => !!$this->bans()->first(),
            'status'          => $this->getOnlineStatus(true),
        ];
    }

}
