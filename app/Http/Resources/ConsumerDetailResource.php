<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ConsumerDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');

        return [
            'id' => $this->id,
            'role' => $this->role,
            'name' => (string)$this->name,
            'userName'=> $this->user_name,
            'preferredPronoun'=> !empty($this->consumer->preferred_pronoun) ? $this->consumer->preferred_pronoun : '',
            'isLocation'=> !empty($this->consumer->location_status) ? $this->consumer->location_status : '',
            'lat'=> !empty($this->consumer->lat) ? $this->consumer->latitude : '',
            'long'=> !empty($this->consumer->long) ? $this->consumer->longitude : '',
            'height'=> !empty($this->consumer->height) ? $this->consumer->height : '',
            'weight'=> !empty($this->consumer->weight) ? $this->consumer->weight : '',
            'gender'=> $this->gender,
            'dateOfBirth'=> $this->date_of_birth,
            'activityLevel'=> !empty($this->consumer->activity_level) ? $this->consumer->activity_level : '',
            'zipcode'=> !empty($this->consumer->zipcode) ? $this->consumer->zipcode : '',
            'profilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->avtar) : asset('admin/images/faces/pic-1.png'),
            'thumbProfilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->avtar) : asset('admin/images/faces/pic-1.png'),
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at
        ];
    }
}
