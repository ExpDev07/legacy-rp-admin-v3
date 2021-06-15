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
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'banHash'   => $this->ban_hash,
            'reason'    => $this->reason,
            'expire'    => $this->expire,
            'expireAt'  => $this->expireAt,
            'timestamp' => $this->timestamp,
            'issuer'    => $this->creator_name ?? null,
        ];
    }

}
