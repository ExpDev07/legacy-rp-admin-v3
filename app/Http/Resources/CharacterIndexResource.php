<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CharacterIndexResource extends JsonResource
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
            'id'              => $this->character_id,
            'steamIdentifier' => $this->steam_identifier,
            'firstName'       => $this->first_name,
            'lastName'        => $this->last_name,
            'dateOfBirth'     => $this->date_of_birth,
            'gender'          => $this->gender,
            'jobName'         => $this->job_name,
            'departmentName'  => $this->department_name,
            'positionName'    => $this->position_name,
            'playerName'      => $this->player_name,
            'phoneNumber'     => $this->phone_number,
            'advanced' => [
                'date_of_birth' => $this->date_of_birth,
                'cash' => $this->cash,
                'bank' => $this->bank,
                'stocks_balance' => $this->stocks_balance,
                'jail' => $this->jail ?? 'null',
            ]
        ];
    }

}
