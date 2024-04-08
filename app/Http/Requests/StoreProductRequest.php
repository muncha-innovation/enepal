<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => ['required'],
            'description' => ['required'],
            'currency' => ['required'],
            'price' => ['required'],
            'image' => $imageValidation,
            'active' => ['required'],
            'business_id' => ['required','exists:businesses,id']
        ];
    }

    public function validated() {
        $data = parent::validated();
        $data['created_by'] = auth()->id();
        // temp slug
        $data['slug'] = \Str::slug($data['name'].uniqid());
        return $data;
    }

    public function messages() {
        return [
            'name.required' => 'Product name is required',
            'description.required' => 'Product description is required',
            'currency.required' => 'Product currency is required',
            'price.required' => 'Product price is required',
            'image.required' => 'Product image is required',
            'image.image' => 'Product image must be an image',
            'image.max' => 'Product image must be less than 2MB',
            'active.required' => 'Product status is required'
        ];
    }

}
