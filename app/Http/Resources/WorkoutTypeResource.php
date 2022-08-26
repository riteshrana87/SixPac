<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class WorkoutTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.WORKOUT_TYPE_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_UPLOAD_PATH');
        
        return [
            'id' => $this->id,
            'title' => $this->name,
            'getfitId' => $this->getfit_id,
            'status' => $this->status,
            'iconFile' => !empty($this->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->icon_file) : "",
            'iconThumbFile' => !empty($this->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->icon_file) : "",
        ];
    }
}
