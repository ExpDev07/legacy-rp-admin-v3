<?php

namespace App\Http\Resources;

use App\Character;
use App\Helpers\GeneralHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CharacterResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $isView = Str::contains($request->path(), '/edit');

        return [
            'id'                         => $this->character_id,
            'steamIdentifier'            => $this->steam_identifier,
            'slot'                       => $this->character_slot,
            'gender'                     => $this->gender,
            'firstName'                  => $this->first_name,
            'lastName'                   => $this->last_name,
            'name'                       => $this->name,
            'phoneNumber'                => $this->phone_number,
            'dateOfBirth'                => $this->date_of_birth,
            'isDead'                     => $this->is_dead,
            'cash'                       => $this->cash,
            'bank'                       => $this->bank,
            'money'                      => $this->money,
            'stocksBalance'              => $this->stocks_balance,
            'jobName'                    => $this->job_name,
            'departmentName'             => $this->department_name,
            'positionName'               => $this->position_name,
            'backstory'                  => $this->backstory,
            'vehicles'                   => VehicleResource::collection($this->vehicles),
            'properties'                 => PropertyResource::collection($this->properties),
            'accessProperties'           => PropertyResource::collection($this->accessProperties()),
            'characterDeleted'           => $this->character_deleted,
            'characterDeletionTimestamp' => $this->character_deletion_timestamp,
            'characterCreationTimestamp' => $this->character_creation_timestamp,
            'licenses'                   => $this->getLicenses(),
            'pedModelHash'               => $this->ped_model_hash ? intval($this->ped_model_hash) : null,
            'outfits'                    => $isView ? Character::getOutfits($this->character_id) : 0,
            'danny'                      => GeneralHelper::isDefaultDanny(intval($this->ped_model_hash), $this->ped_model_data),
            'mugshot'                    => $this->mugshot_url ?? null,
            'playtime'                   => $this->playtime,
        ];
    }
}
