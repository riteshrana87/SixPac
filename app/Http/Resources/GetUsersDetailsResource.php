<?php

namespace App\Http\Resources;

use App\Models\UserFollower;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class GetUsersDetailsResource extends JsonResource
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
            'name'=> (string)$this->name,
            'userName' => (string)$this->user_name,
            // 'follower_status'=>UserFollower::followingUserGet($this->id),
            // 'following_status'=>UserFollower::followeUserGet($this->id),
            'profilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->avtar) : asset('admin/images/faces/pic-1.png'),
            'thumbProfilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->avtar) : asset('admin/images/faces/pic-1.png'),
        ];
    }
}
