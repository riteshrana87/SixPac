<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'categoryId' => $this->category_id,
            'productTitle' => (string)$this->product_title,
            'productSlug' => $this->product_slug,
            'productDescription' => $this->product_description,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'costPrice' => $this->cost_price,
            'sellPrice' => $this->sell_price,
            'galleriesCount' => $this->product_galleries_count,
            'galleries' => productGalleriesResource::collection($this->productGalleries),
            'users' => new GetUsersDetailsResource($this->user),
            'createdAt' => (string) $this->created_at,
        ];
    }
}
