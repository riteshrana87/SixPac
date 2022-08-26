<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class GetFitExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.EXERCISE_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.EXERCISE_THUMB_PHOTO_UPLOAD_PATH');
        
        if($this->gender == 1){
            $gender = "Male";
        }else if($this->gender == 2){
            $gender = "Female";
        }else{
            $gender = "Both";
        }


        if($this->location == 1){
            $location = "Gym";
        }else if($this->gender == 2){
            $location = "Home";
        }else{
            $location = "Anywhere";
        }
        
        $arr = [];
        $arr['id'] = $this->id;
        $arr['name'] = $this->name;
        $arr['location'] = $location;
        $arr['posterImage'] = !empty($this->poster_image) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->poster_image) : '';
        $arr['videoName'] = !empty($this->video_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->video_name) : '';
        $arr['videoUrl'] = !empty($this->video_url) ? $this->video_url : '';
        $arr['overview'] = $this->overview;
        $arr['gender'] = $gender;
        $arr['workoutTypeId'] = $this->workout_type_id;
        $arr['workoutTypeName'] = !empty($this->workoutType->name) ? $this->workoutType->name : '';
        $arr['durationId'] = $this->duration_id;
        $arr['status'] = $this->status;
        $arr['interests'] = GetFitInterestsResource::collection($this->interests);
        $arr['equipments'] = GetFitEquipmentsResource::collection($this->equipments);
        $arr['bodyParts'] = BodyPartsReource::collection($this->bodyParts);
        $arr['ageGroups'] = AgeGroupsResource::collection($this->ageGroups);
        $arr['fitnessLevels'] = FitnessLevelsResource::collection($this->fitnessLevels);
        return $arr;
    }
}
