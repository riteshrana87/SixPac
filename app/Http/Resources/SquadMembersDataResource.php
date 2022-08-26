<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class SquadMembersDataResource extends JsonResource
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
        if($this->user){
            return [
                'squadMemberId' => $this->id,
                'userId' => $this->user->id,
                'name'=> (string)$this->user->name,
                'userName' => (string)$this->user->user_name,
                'profilePic' => !empty($this->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->user->avtar) : asset('admin/images/faces/pic-1.png'),
                'thumbProfilePic' => !empty($this->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->user->avtar) : asset('admin/images/faces/pic-1.png'),
                'leaderMemberFlag' => $this->leader_member_flag,
                'status' => $this->status,
            ];
        }
        
    }
}
