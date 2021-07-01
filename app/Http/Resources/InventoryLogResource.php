<?php

namespace App\Http\Resources;

use App\Inventory;
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
            'timestamp'       => $this->timestamp,
            'steamIdentifier' => $this->identifier,
            'inventoryFrom'   => Inventory::parseLogDetails($this->details, 'from'),
            'inventoryTo'     => Inventory::parseLogDetails($this->details, 'to'),
            'itemMoved'       => Inventory::parseItem($this->details),
        ];
    }

}
