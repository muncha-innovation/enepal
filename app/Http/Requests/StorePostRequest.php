<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
        if($this->isMethod('post')) {
            $imageValidation = 'required|image|max:1999';
        } else {
            $imageValidation = 'sometimes|image|max:1999';
        }
        return [
            'title' => ['required'],
            'content' => ['required'],
            'short_description' => ['required'],
            'image' => $imageValidation,
            'business_id' => ['required'],
            'is_active' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Post title is required',
            'content.required' => 'Post content is required',
            'short_description.required' => 'Post short description is required',
            'image.required' => 'Post image is required',
            'image.image' => 'Post image must be an image',
            'image.max' => 'Post image must be less than 2MB',
            'business_id.required' => 'Business is required',
            'is_active.required' => 'Post status is required'
            
        ];
    }

    public function validated() {
        $data = parent::validated();
        $data['user_id'] = auth()->id();
        if(isset($data['title']['en']))  {
            $slug = \Str::slug($data['title']['en']).'-'.uniqid();
        } else if(isset($data['title']['np'])) {
            $slug = \Str::slug($data['title']['np'],'-','np').'-'.uniqid();
        } else {
            $slug = uniqid();
        }
        $data['slug'] = $slug;
        return $data;
    }
}
