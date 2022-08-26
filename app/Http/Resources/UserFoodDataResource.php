<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use phpseclib3\Crypt\PublicKeyLoader;

class UserFoodDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //$public = PublicKeyLoader::load(config('constant.RSA_PUBLIC_KEY'));
        return [
            // 'FDIID' => !empty($this->fdiid) ? base64_encode($public->encrypt($this->fdiid)) : '',
            // 'Protein' => !empty($this->protein) ? base64_encode($public->encrypt($this->protein)) : '',
            // 'Carbohydrate' => !empty($this->carbohydrate) ? base64_encode($public->encrypt($this->carbohydrate)) : '',
            // 'Calory' => !empty($this->calory) ? base64_encode($public->encrypt($this->calory)) : '',
            // 'Energy' => !empty($this->energy) ? base64_encode($public->encrypt($this->energy)) : '',
            // 'typeOfMeal' => !empty($this->type_of_meal) ? base64_encode($public->encrypt($this->type_of_meal)) : '',
            // 'DateAndTime' => !empty($this->date_and_time) ? base64_encode($public->encrypt($this->date_and_time)) : '',
            // 'FoodDescription' => !empty($this->food_description) ? base64_encode($public->encrypt($this->food_description)) : '',
            // 'created_at'=> $this->created_at,
            // 'updated_at'=> $this->updated_at,
            'id' => !empty($this->id) ? $this->id : '',
            'FDIID' => !empty($this->fdiid) ? $this->fdiid : '',
            'Protein' => !empty($this->protein) ? $this->protein : '',
            'Carbohydrate' => !empty($this->carbohydrate) ? $this->carbohydrate : '',
            'Calory' => !empty($this->calory) ? $this->calory : '',
            'Energy' => !empty($this->energy) ? $this->energy : '',
            'typeOfMeal' => !empty($this->type_of_meal) ? $this->type_of_meal : '',
            'DateAndTime' => !empty($this->date_and_time) ? $this->date_and_time : '',
            'FoodDescription' => !empty($this->food_description) ? $this->food_description : '',
            'quantity' => !empty($this->quantity) ? $this->quantity : '',
            'foodOrExercise' => !empty($this->food_or_exercise) ? $this->food_or_exercise : 0,
            'servingQty' => !empty($this->serving_qty) ? $this->serving_qty : 0,
            'servingSize' => !empty($this->serving_size) ? $this->serving_size : 0,
            'notes' => !empty($this->notes) ? $this->notes : '',
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at,
        ];

    }
}
