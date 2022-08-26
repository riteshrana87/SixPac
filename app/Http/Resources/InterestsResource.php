<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class InterestsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.INTEREST_THUMB_PHOTO_UPLOAD_PATH');

        return [
            'interestId' => $this->id,
            'interestName' => $this->interest_name,
            'imageUrl' => !empty($this->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->icon_file) : asset('backend/assets/images/no-icon.png'),
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at,
            'subInterest'=> !empty($this->subinterests) ? $this->subinterests : '',
            //'subInterest'=> !empty($this->subinterests) ? SubInterestsResource::collection($this->subinterests) : '',
        ];
    }
}
