<?php

namespace App\Http\Resources;

use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PanelLogResource extends JsonResource
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
            'log'       => $this->log,
            'timestamp' => $this->timestamp,
        ];
    }

}
