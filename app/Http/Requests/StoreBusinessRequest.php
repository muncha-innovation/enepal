<?php

namespace App\Http\Requests;

use App\Enums\SettingKeys;
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
        // cover_image and logo required only during create and not edit
        if($this->isMethod('post')) {
            $coverImageValidation = 'required|image|max:1999';
            $logoValidation = 'required|image|max:1999';
        } else {
            $coverImageValidation = 'sometimes|image|max:1999';
            $logoValidation = 'sometimes|image|max:1999';
        }
        return [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'type_id' => ['required',],
            'phone_1' => ['required'],
            'active' => ['required'],
            'description' => ['array', 'sometimes'],
            'cover_image' => $coverImageValidation,
            'logo' => $logoValidation,
            'phone_2'  => ['sometimes'],
            'address.city' => ['sometimes'],
            'address.state_id' => ['sometimes'],
            'address.street' => ['sometimes'],
            'address.postal_code' => ['sometimes'],
            'address.address_line_1' => ['sometimes'],
            'address.address_line_2' => ['sometimes'],
            'address.country_id' => ['required'],
            'address.prefecture' => ['sometimes'],
            'address.town' => ['sometimes'],
            'address.building' => ['sometimes'],
            'address.latitude' => ['sometimes'],
            'address.longitude' => ['sometimes'],
            'settings' => ['sometimes']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Business name is required',
            'email.required' => 'Business email is required',
            'type_id.required' => 'Business type is required',
            'phone_1.required' => 'Business phone number is required',
            'active.required' => 'Business status is required',
            'cover_image.required' => 'Business cover image is required',
            'logo.required' => 'Business logo is required',
            'address.country_id.required' => 'Country is required',
        ];
    }

    public function validated()
    {
        $data = parent::validated();
        $default_settings = [
            SettingKeys::MAX_NOTIFICATION_PER_DAY => 0,
            SettingKeys::MAX_NOTIFICATION_PER_MONTH => 0,
        ];
        $data['settings'] = isset($data['settings'])? $data['settings'] : $default_settings;
        return $data;
    }
}
