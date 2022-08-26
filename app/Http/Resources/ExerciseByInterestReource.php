<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseByInterestReource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            [
                "id"=> 1,
                "name"=> "Abs",
                "iconFile"=> "http://10.37.54.103:8080/storage/uploads/body_part/original/abs.jpg",
                "status"=> 1,
            ],
            [
                "id"=> 2,
                "name"=> "Arms",
                "iconFile"=> "http://10.37.54.103:8080/storage/uploads/body_part/original/arms.jpg",
                "status"=> 1,
            ],
            [
                "id"=> 3,
                "name"=> "Back",
                "iconFile"=> "http://10.37.54.103:8080/storage/uploads/body_part/original/back.jpg",
                "status"=> 1,
            ],
            [
                "id"=> 4,
                "name"=> "Chest",
                "iconFile"=> "http://10.37.54.103:8080/storage/uploads/body_part/original/chest.jpg",
                "status"=> 1,
            ],
            [
                "id"=> 5,
                "name"=> "Legs",
                "iconFile"=> "http://10.37.54.103:8080/storage/uploads/body_part/original/legs.jpg",
                "status"=> 1,
            ]
        ];

        return $data;
    }
}
