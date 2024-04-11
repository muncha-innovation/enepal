<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessTypeRequest extends FormRequest
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
            'title' => ['required'],
            'facilities' => ['required', 'array'],
            'facilities.*' => ['exists:facilities,id'],
            'icon' => 'nullable | image | mimes:png,jpg,jpeg,svg,bmp | max:2048',
        ];
    }

    public function messages()
    {
        return [
            'facilities.*.exists' => 'The selected facility is invalid.',
            'title.required' => 'The name field is required.',
            'facilities.required' => 'The facilities field is required.',

        ];
    }
}
