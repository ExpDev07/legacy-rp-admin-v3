<?php

namespace App\Http\Resources;

use App\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TwitterPostResource extends JsonResource
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
            'id'       => $this->id,
            'authorId' => $this->authorId,
            'realUser' => $this->realUser,
            'message'  => $this->message,
            'time'     => $this->time,
            'likes'    => $this->likes,
        ];
    }

}
