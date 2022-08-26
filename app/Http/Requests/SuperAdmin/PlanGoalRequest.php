<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class PlanGoalRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:4|max:70|unique:plan_goals,name,'.$this->id,
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
            'name.required' => 'The plan goal is required.',
            'name.min' => 'The plan goal must be at least 4 characters.',
            'name.max' => 'The plan goal must not be greater than 70 characters.',
            'name.unique' => 'The plan goal has already been taken.',
            'icon_file.required' => 'Plan goal icon is required.',
            'icon_file.mimes' => 'The Plan goal icon must be a file of type: jpeg, png.',
            'icon_file.dimensions' => 'The Plan goal icon has invalid image dimensions.'
        ];
    }
}
