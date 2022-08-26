<?php

namespace App\Http\Resources;

use App\Models\OtpVerified;
use App\Models\UserFollower;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class UsersResource extends JsonResource
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
            'userId' => $this->id,
            'role' => $this->role,
            'email'=> $this->email,
            'referralCode'=> !empty($this->referral_code) ? $this->referral_code : '',
            'name' => $this->name,
            'userName' => $this->user_name,
            'age' => 30,
            'gender'=> $this->gender,
            'genderPronoun'=> !empty($this->gender_pronoun) ? $this->gender_pronoun : '',
            'isEmailVerified' =>!empty($this->is_email_verified) ? $this->is_email_verified : '',
            'isMobileVerified' => !empty($this->is_mobile_verified) ? $this->is_mobile_verified : '',
            'otpVerified'=>$this->otp_verified,
            'birthdate'=> (string)$this->date_of_birth,
            'phone'=> (string)$this->phone,
            'quickBloxId'=> (string)$this->quick_blox_id,
            'fitnessStatus' => !empty($this->consumer->fitness_status) ? $this->consumer->fitness_status : '',
            'isLocation'=> !empty($this->consumer->location_status) ? $this->consumer->location_status : '',
            'lat'=> !empty($this->consumer->lat) ? $this->consumer->latitude : '',
            'long'=> !empty($this->consumer->long) ? $this->consumer->longitude : '',
            'height'=> !empty($this->consumer->height) ? $this->consumer->height : '',
            'weight'=> !empty($this->consumer->weight) ? $this->consumer->weight : '',
            'updateData' => !empty($this->consumer->update_data) ? $this->consumer->update_data : '',
            'socialFlag'=>$this->social_flag,
            'status'=>$this->status,
            'profilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->avtar) : asset('admin/images/faces/pic-1.png'),
            'thumbProfilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->avtar) : asset('admin/images/faces/pic-1.png'),
            'follower_status'=>UserFollower::checkUserfollower($this->id),
            'following_status'=>UserFollower::checkUserfollowing($this->id),
        ];
    }
}
