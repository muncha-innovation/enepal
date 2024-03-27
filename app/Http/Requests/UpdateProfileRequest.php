<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'country' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email,' . auth()->id(),
            'phone' => 'required',
            'password' => 'nullable|confirmed',
            'profile_picture' => 'nullable|image|mimes:png,jpg,jpeg'
        ];
    }

    public function validated() {
        $data = parent::validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        return $data;
    }
}
