<?php

namespace App\Http\Resources\Client\User;

use Illuminate\Http\Resources\Json\JsonResource;

class Short extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }
}
