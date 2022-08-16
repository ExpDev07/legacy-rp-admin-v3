<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'id'            => $this->vehicle_id,
            'owner_cid'     => $this->owner_cid,
            'garage_name'   => $this->garage(),
            'model_name'    => vehicle_model_name($this->model_name),
            'plate'         => $this->plate,
            'modifications' => $this->getModifications(),
            'fuel'          => intval($this->deprecated_fuel) ?? 0,
            'emergency'     => intval($this->emergency_type) ?? 0
        ];
    }

}
