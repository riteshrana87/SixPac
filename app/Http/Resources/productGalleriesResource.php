<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class productGalleriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.PRODUCTS_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.PRODUCTS_THUMB_PHOTO_UPLOAD_PATH');
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'originalFile' => !empty($this->file_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->file_name) : '',
            'thumbFile' => !empty($this->thumb_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->thumb_name) : '',
            'file_type' => $this->file_type,
            'fileSize' => $this->file_size,
        ];
    }
}
