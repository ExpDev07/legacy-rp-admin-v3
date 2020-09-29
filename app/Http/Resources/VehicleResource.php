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
            'id'          => $this->vehicle_id,
            'garage_name' => $this->garage_name,
            'model_name'  => $this->model_name,
            'plate'       => $this->plate,
        ];
    }

}
