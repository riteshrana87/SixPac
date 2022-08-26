<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'categoryName' => $this->category_name,
            'createdBy' => (string)$this->created_by,
            'status'=> $this->status,
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at
        ];
    }
}
