<?php

namespace App\Http\Resources;

use App\Models\OtpVerified;
use App\Models\User;
use App\Models\UserFollower;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class UserRegisterResource extends JsonResource
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

        $bannerOriginalImagePath = Config::get('constant.BANNER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $bannerThumbImagePath = Config::get('constant.BANNER_THUMB_PHOTO_UPLOAD_PATH');

        return [
            'userId' => $this->id,
            'role' => $this->role,
            'email'=> !empty($this->email) ? $this->email : '',
            'referralCode'=> !empty($this->referral_code) ? $this->referral_code : '',
            'name' => $this->name,
            'userName' => $this->user_name,
            'age' => 30,
            'gender'=> $this->gender,
            'genderPronoun'=> !empty($this->gender_pronoun) ? $this->gender_pronoun : '',
            // 'genderPronoun'=> !empty($this->consumer->preferred_pronoun) ? $this->consumer->preferred_pronoun : '',
            'isEmailVerified' =>!empty($this->is_email_verified) ? $this->is_email_verified : '',
            'isMobileVerified' => !empty($this->is_mobile_verified) ? $this->is_mobile_verified : '',
            'otpVerified'=>$this->otp_verified,
            'postalCode'=> !empty($this->consumer->zipcode) ? $this->consumer->zipcode : '',
            'interests'=> User::interests($this->id),
            'birthdate'=> (string)$this->date_of_birth,

            'dailyCalories' => !empty($this->consumer->daily_calories) ? $this->consumer->daily_calories : 0,
            'burnedCalory' => !empty($this->consumer->burned_calory) ? $this->consumer->burned_calory : 0,
            'targetWeight' => !empty($this->consumer->target_weight) ? $this->consumer->target_weight : '',
            'startingWeight' => !empty($this->consumer->starting_weight) ? $this->consumer->starting_weight : '',
            'weightGainLossFrequency'=> !empty($this->consumer->weight_gain_loss_frequency) ? $this->consumer->weight_gain_loss_frequency : '',
            'weightGoalRaw' => !empty($this->consumer->weight_goal) ? $this->consumer->weight_goal : '',
            'acitivityFrequency' => !empty($this->consumer->activity_frequency) ? $this->consumer->activity_frequency : '',
            'activityLevel'=> !empty($this->consumer->activity_level) ? $this->consumer->activity_level : '',
            'phone'=> (string)$this->phone,
            'quickBloxId'=> (string)$this->quick_blox_id,
            'preferredPronoun'=> !empty($this->consumer->preferred_pronoun) ? $this->consumer->preferred_pronoun : '',
            'fitnessStatus' => !empty($this->consumer->fitness_status) ? $this->consumer->fitness_status : '',
            'isLocation'=> !empty($this->consumer->location_status) ? $this->consumer->location_status : '',
            'city'=> !empty($this->consumer->city) ? $this->consumer->city : '',
            'state'=> !empty($this->consumer->state) ? $this->consumer->state : '',
            'country'=> !empty($this->consumer->country) ? $this->consumer->country : '',
            // 'city_name'=> getCityName($this->consumer->city),
            // 'state_name'=> getStateName($this->consumer->state),
            // 'country_name'=> getCountryName($this->consumer->country),

            'cityName'=> !empty($this->consumer->city) ? getCityName($this->consumer->city) : '',
            'stateName'=> !empty($this->consumer->state) ? getStateName($this->consumer->state) : '',
            'countryName'=> !empty($this->consumer->country) ? getCountryName($this->consumer->country) : '',

            'lat'=> !empty($this->consumer->latitude) ? $this->consumer->latitude : '',
            'long'=> !empty($this->consumer->longitude) ? $this->consumer->longitude : '',
            'height'=> !empty($this->consumer->height) ? $this->consumer->height : '',
            'weight'=> !empty($this->consumer->weight) ? $this->consumer->weight : '',
            'updateData' => !empty($this->consumer->update_data) ? $this->consumer->update_data : '',
            'socialFlag'=>$this->social_flag,
            'status'=>$this->status,
            'goalCompletionDate' => !empty($this->consumer->goal_completion_date) ? $this->consumer->goal_completion_date : '',
            'MeasurementType' => !empty($this->consumer->measurement_type) ? $this->consumer->measurement_type : 0,
            'profilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->avtar) : '',
            'thumbProfilePic' => !empty($this->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->avtar) : '',


            'companyName' => !empty($this->business->company_name) ? $this->business->company_name : '',
            'companyUrl' => !empty($this->business->company_url) ? $this->business->company_url : '',

            'bannerPic' => !empty($this->banner_pic) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($bannerOriginalImagePath.$this->banner_pic) : '',
            'thumbBannerPic' => !empty($this->banner_pic) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($bannerThumbImagePath.$this->banner_pic) : '',
            'follower_status'=>UserFollower::checkUserfollower($this->id),
            'following_status'=>UserFollower::checkUserfollowing($this->id),
            'createdBy'=> $this->created_by,
            'updatedBy'=> $this->updated_by,
            'createdAt'=> $this->created_at,
            'updatedAt'=> $this->updated_at,

        ];
    }
}
