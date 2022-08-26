<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:4|max:70|unique:equipments,name,'.$this->id,
            // 'getfit_id' => 'required|exists:getfit,id',
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
            'name.required' => 'The equipment is required.',
            'name.min' => 'The equipment must be at least 4 characters.',
            'name.max' => 'The equipment must not be greater than 70 characters.',
            'name.unique' => 'The equipment has already been taken.',
            // 'getfit_id.required' => 'The getfit type is required.',
            // 'getfit_id.exists' => 'The selected getfit type is invalid.',
            'icon_file.required' => 'type icon is required.',
            'icon_file.mimes' => 'The type icon must be a file of type: jpeg, png.',
            'icon_file.dimensions' => 'The type icon has invalid image dimensions.'
        ];
    }
}