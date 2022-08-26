<?php

namespace App\Http\Resources;

use App\Models\Squad;
use App\Models\SquadMembers;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class SquadDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.SQUAD_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.SQUAD_THUMB_PHOTO_UPLOAD_PATH');

        $BannerOriginalImagePath = Config::get('constant.SQUAD_BANNER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $BannerThumbImagePath = Config::get('constant.SQUAD_BANNER_THUMB_PHOTO_UPLOAD_PATH');

        return [
            'squadId' => $this->id,
            'squadName' => $this->squad_name,
            'notes' => $this->notes,
            'status' => $this->status,
            'isPublic' => $this->is_public,
            //'squadProfilePic' => $this->squad_profile_pic,
            'squadProfilePic' => !empty($this->squad_profile_pic) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->squad_profile_pic) : "",
            'squadThumbProfilePic' => !empty($this->squad_profile_pic) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->squad_profile_pic) : "",
            'squadBannerPic' => !empty($this->banner_pic) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($BannerOriginalImagePath.$this->banner_pic) : "",
            'squadThumbBannerPic' => !empty($this->banner_pic) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($BannerThumbImagePath.$this->banner_pic) : "",
            'isMember' =>  SquadMembers::isMember($this->id),
            'requestStatus' => SquadMembers::requestStatus($this->id),
            'leaderMembersUserName' => Squad::getLeaderMembers($this->id),
            'memberList' => SquadMembersDataResource::collection($this->members),
            //'leaderMembersList' => SquadMembersDataResource::collection($this->leaderMembers),

            
        ];
    }
}
