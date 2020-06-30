<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BanResource extends JsonResource
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
            'id'        => $this->id,
            'banHash'   => $this->ban_hash,
            'reason'    => $this->reason,
            'timestamp' => $this->timestamp,
            'issuer'    => $this->issuer->player_name ?? null,
        ];
    }

}
