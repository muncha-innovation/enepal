<?php

namespace App\Http\Requests;

use App\Services\DocumentService;
use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
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
        if($this->isMethod('PUT')) {
            $coverImageValidation = 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } else {
            $coverImageValidation = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }
        return [
            'title' => 'required',
            'images' => 'sometimes|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'cover_image' => $coverImageValidation,
            'is_active' => 'required|boolean',
            'is_private' => 'required|boolean',
            'business_id' => 'required|exists:businesses,id',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('images')) {
            $images = collect($this->get('images'))->map(function ($image) {
                if (is_string($image)) {
                    return getUploadedFileFromBase64($image);
                }
            })->toArray();
            $this->merge(['images' => $images]);
        }
    }


    public function validated()
    {
        $request = parent::validated();
        $images = [];
        $imageService = new DocumentService();
        $businessId = $this->business_id;
        if ($this->has('images')) {
            foreach ($this->images as $key => $image) {
                $url = $imageService->store($image, 'gallery/' . $businessId);
                $images[$key]['original_filename'] = $this->images_name[$key];
                $images[$key]['business_id'] = $this->business_id;
                $images[$key]['image'] = $url;
            }
        }
        if($this->has('cover_image')) {
            $coverImage = $imageService->store($this->cover_image, 'gallery/' . $businessId);
        }
        unset($request['images']);
        unset($request['cover_image']);
        $request['business_id'] = $businessId;
        $request['user_id'] = auth()->id();
        if(isset($coverImage)) {
            $request['cover_image'] = $coverImage;
        }
        $request['images'] = $images;

        return $request;
    }

    public function messages() {
        return [
            'images.*.file' => 'The images must be a file.',
            'images.*.mimes' => 'The images must be a file of type: jpeg, png, jpg, gif, svg.',
            'images.*.max' => 'The images may not be greater than 2048 kilobytes.',
            'cover_image.required' => 'The cover image field is required.',
            'cover_image.image' => 'The cover image must be an image.',
            'cover_image.mimes' => 'The cover image must be a file of type: jpeg, png, jpg, gif, svg.',
            'cover_image.max' => 'The cover image may not be greater than 2048 kilobytes.',
            'is_active.required' => 'The is active field is required.',
            'is_active.boolean' => 'The is active field must be true or false.',
            'is_private.required' => 'The is private field is required.',
            'is_private.boolean' => 'The is private field must be true or false.',
            'business_id.required' => 'The business id field is required.',
            'business_id.exists' => 'The selected business id is invalid.',
        ];
    }

}
