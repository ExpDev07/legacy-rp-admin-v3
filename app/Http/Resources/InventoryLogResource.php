<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryLogResource extends JsonResource
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
            'id'              => $this->id,
            'details'         => $this->details,
            'timestamp'       => $this->timestamp,
            'steamIdentifier' => $this->identifier,
            'playerName'      => $this->player_name,
        ];
    }

}
