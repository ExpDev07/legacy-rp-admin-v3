<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
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
            'property_id'                  => $this->property_id,
            'property_address'    => $this->property_address,
            'property_cost'       => vehicle_model_name($this->property_cost),
            'property_renter_cid' => $this->property_renter_cid,
            'property_income'     => $this->property_income,
        ];
    }

}
