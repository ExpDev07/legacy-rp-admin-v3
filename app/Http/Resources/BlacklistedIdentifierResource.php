<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlacklistedIdentifierResource extends JsonResource
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
            'id'         => $this->blacklist_id,
            'identifier' => $this->identifier,
            'creator'    => new PlayerIndexResource($this->creator()->first()),
            'reason'     => $this->reason,
            'timestamp'  => $this->timestamp,
        ];
    }

}
