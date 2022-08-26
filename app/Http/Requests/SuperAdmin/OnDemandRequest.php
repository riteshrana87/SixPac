<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class OnDemandRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service' => 'required|min:4|max:70|unique:on_demand_services,service,'.$this->id,
            'getfit_id' => 'required|exists:getfit,id',
            'status' => 'required|in:0,1'
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
            'service.required' => 'The service is required.',
            'service.min' => 'The service must be at least 4 characters.',
            'service.max' => 'The service must not be greater than 70 characters.',
            'service.unique' => 'The service has already been taken.',
            'getfit_id.required' => 'The getfit category is required.',
            'getfit_id.exists' => 'The selected getfit category is invalid.'
        ];
    }
}
