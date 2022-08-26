<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumerBodyDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'height'=> $this->consumer->height,
            'weight'=> $this->consumer->weight,
            'activityLevel'=> $this->consumer->activity_level,
            'gender'=> $this->gender,
            'dateOfBirth'=> $this->date_of_birth,
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at
        ];
    }
}
