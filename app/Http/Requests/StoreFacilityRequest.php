<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'business_types' => ['required', 'array'],
            'business_types.*' => ['exists:business_types,id'],
            'input_type' => ['required'],
            'icon' => 'nullable | image | mimes:png,jpg,jpeg,svg,bmp | max:2048',
        ];
    }

    public function messages()
    {
        return [
            'business_types.*.exists' => 'The selected business type is invalid.',
            'input_type.required' => 'The input type field is required.',
            'name.required' => 'The name field is required.',
        ];
    }
}
