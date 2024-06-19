<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
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
        
        $imageValidation = 'sometimes|image|max:1999';
        return [
            'title' => ['required'],
            'content' => ['required'],
            'image' => $imageValidation,
            'is_active' => ['sometimes'],
            'business_id' => ['required','exists:businesses,id']
        ];
    }

    public function validated() {
        $data = parent::validated();

        return $data;
    }

    public function messages() {
        return [
            'name.required' => 'Notification title is required',
            'description.required' => 'Notification content is required',
            'image.required' => 'Notification image is required',
            'image.image' => 'Notification image must be an image',
            'image.max' => 'Notification image must be less than 2MB',
            'is_active.required' => 'Notification status is required'
        ];
    }
}
