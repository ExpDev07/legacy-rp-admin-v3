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
		$keys = [];

		$sharedKeys = $this->shared_keys ? explode(';', $this->shared_keys) : [];

		foreach ($sharedKeys as $key) {
			$data = explode('-', $key);

			if (sizeof($data) >= 3) {
				$level = intval($data[1]);
				$cid = intval($data[2]);

				$keys["c_" . $cid] = $level;
			}
		}

        return [
            'property_id'         => $this->property_id,
            'property_address'    => $this->property_address,
            'property_cost'       => $this->property_cost,
            'property_renter_cid' => $this->property_renter_cid,
            'property_income'     => $this->property_income,
            'property_last_pay'   => $this->property_last_pay,
			'keys'                => $keys,
        ];
    }

}
