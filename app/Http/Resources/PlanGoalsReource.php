<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class PlanGoalsReource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.PLAN_GOAL_ORIGINAL_PHOTO_UPLOAD_PATH');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'iconFile' => !empty($this->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->icon_file) : '',
            'status' => $this->status,
        ];
    }
}
