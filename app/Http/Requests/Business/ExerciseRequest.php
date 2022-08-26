<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class ExerciseRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:4|max:70|unique:exercises,name,'.$this->id,
            'overview' => 'required|min:4|max:500',
            'workout_type_id' => 'required|exists:workout_types,id',
            'interests' => 'required|exists:interests,id',
            'duration_id' => 'required|exists:exercise_durations,id',
            'equipment' => 'required|exists:equipments,id',
            'body_part' => 'required|exists:body_parts,id',
            'age_group' => 'required|exists:age_groups,id',
            'fitness_level' => 'required|exists:fitness_levels,id',
            'gender' => 'required|in:1,2,3',
            'location' => 'required|in:1,2,3',
            'video_name' => (empty($this->video_name) && empty($this->old_video) ? 'required|' : '').'mimes:mp4,mov,mkv,webm',
            'poster_image' => (empty($this->poster_image) && empty($this->old_image) ? 'required|' : '').'mimes:jpeg,png|max:1024|dimensions:min_width=200,max_width=1000',
            'status'=>'required|in:0,1'
        ];
    }

     /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The exercise title is required.',
            'name.min' => 'The exercise title must be at least 4 characters.',
            'name.max' => 'The exercise title must not be greater than 70 characters.',
            'name.unique' => 'The exercise title has already been taken.',
            'video_name.required'=>'The exercise video field is required.',           
            'video_name.mimes'=>'The video name must be a file of type: mp4, mov, mkv, webm.',
            'workout_type_id.required' => 'The workout type field is required.',
            'duration_id.required' => 'The duration field is required.',
        ];
    }
}