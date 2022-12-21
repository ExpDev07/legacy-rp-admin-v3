<?php

namespace App\Http\Resources;

use App\Ban;
use App\Player;
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
        $status = Player::getOnlineStatus($this->license_identifier, true);

        return [
            'licenseIdentifier' => $this->license_identifier,
            'playerName'      => $status && $status->fakeName ? $status->fakeName : $this->player_name,
            'playTime'        => $this->playtime,
            'warnings'        => $this->warning_count,
            'isBanned'        => !!Ban::getBanForUser($this->license_identifier),
            'status'          => $status,
            'playtime'        => $this->playtime
        ];
    }

}
