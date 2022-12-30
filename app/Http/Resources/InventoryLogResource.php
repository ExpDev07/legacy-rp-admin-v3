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
		$metadata = isset($this->metadata) ? json_decode($this->metadata, true) : false;

		$inventoryFrom = $metadata && isset($metadata["startInventory"]) ? $metadata["startInventory"] : null;
		$inventoryTo = $metadata && isset($metadata["endInventory"]) ? $metadata["endInventory"] : null;

		if (!$metadata) {
			$metadata = isset($this->meta) ? json_decode($this->meta, true) : false;
		}

        return [
            'timestamp'       => $this->timestamp,
            'licenseIdentifier' => $this->identifier,
            'inventoryFrom'   => $inventoryFrom ?? Inventory::parseLogDetails($this->details, 'from'),
            'inventoryTo'     => $inventoryTo ?? Inventory::parseLogDetails($this->details, 'to'),
            'itemMoved'       => Inventory::parseItem($this->details),
			'metadata' => $metadata,
        ];
    }

}
