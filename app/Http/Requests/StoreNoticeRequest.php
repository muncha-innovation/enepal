<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoticeRequest extends FormRequest
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
            'is_active' => ['required'],
            'is_private' => ['required'],
            'business_id' => ['required','exists:businesses,id']
        ];
    }

    public function validated() {
        $data = parent::validated();
        $data['user_id'] = auth()->id();
        return $data;
    }

    public function messages() {
        return [
            'name.required' => 'Notice title is required',
            'description.required' => 'Notice content is required',
            'image.required' => 'Notice image is required',
            'image.image' => 'Notice image must be an image',
            'image.max' => 'Notice image must be less than 2MB',
            'is_active.required' => 'Notice status is required'
        ];
    }

}
