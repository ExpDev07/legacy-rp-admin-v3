<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarningResource extends JsonResource
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
            'message'   => $this->message,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'player'    => new PlayerResource($this->player),
            'issuer'    => new PlayerResource($this->issuer),
        ];
    }

}
