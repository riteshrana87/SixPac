<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FoodsMasterResource extends JsonResource
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
            'fdcId' => $this->fdc_id,
            'description' => $this->description,
            'brandName' => $this->brand_name,
            'upinGstin' => $this->upin_gstin,
            'servingSize' => $this->serving_size,
            'servingQty' => $this->serving_qty,
            'energy' => $this->energy,
            'protein' => $this->protein,
            'carbohydrate' => $this->carbohydrate,
        ];
    }
}
