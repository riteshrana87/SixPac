<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class PlanSportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:4|max:70|unique:plan_sports,name,'.$this->id,
            'status' => 'required|in:0,1',
            'icon_file' => (empty($this->icon_file) && empty($this->old_icon) ? 'required|' : '').'mimes:jpeg,png|max:1024|dimensions:min_width=100,max_width=800'
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
            'name.required' => 'The plan sport is required.',
            'name.min' => 'The plan sport must be at least 4 characters.',
            'name.max' => 'The plan sport must not be greater than 70 characters.',
            'name.unique' => 'The plan sport has already been taken.',
            'icon_file.required' => 'Plan sport icon is required.',
            'icon_file.mimes' => 'The Plan sport icon must be a file of type: jpeg, png.',
            'icon_file.dimensions' => 'The Plan sport icon has invalid image dimensions.'
        ];
    }
}
