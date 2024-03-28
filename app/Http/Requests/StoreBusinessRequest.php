<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'name' => ['required'],
            'country' => ['required'],
            'type_id' => ['required',],
            'phone_1' => ['required'],
            'cover_image' => ['required', 'image', 'max:1999'],
            'logo' => ['required', 'image', 'max:1999'],
            'phone_2'  => ['sometimes'],
            'address.city' => ['sometimes']
        ];
    }
}
