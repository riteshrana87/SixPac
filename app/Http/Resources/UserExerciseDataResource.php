<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExerciseDataResource extends JsonResource
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
            'id' => !empty($this->id) ? $this->id : '',
            'userId' => !empty($this->user_id) ? $this->user_id : '',
            'exerciseId' => !empty($this->exercise_id) ? $this->exercise_id : '',
            'calory' => !empty($this->calory) ? $this->calory : '',
            'timeSpend' => !empty($this->time_spend) ? $this->time_spend : '',
            'notes' => !empty($this->notes) ? $this->notes : '',
            'description' => !empty($this->description) ? $this->description : '',
            'met' => !empty($this->met) ? $this->met : '',
            'DateAndTime' => !empty($this->date_and_time) ? $this->date_and_time : '',
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at,
        ];
    }
}
