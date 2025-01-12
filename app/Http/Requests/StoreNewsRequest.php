<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'boolean',
            'url' => 'nullable|url',
            'image' => 'nullable|url',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:news_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'language' => 'nullable|string|max:4',
            'locations' => 'nullable|array',
            'locations.*.name' => 'required|string|max:255',
            'locations.*.latitude' => 'required|numeric',
            'locations.*.longitude' => 'required|numeric',
            'locations.*.radius' => 'required|numeric',
            'age_groups' => 'nullable|array',
            'age_groups.*' => 'exists:age_groups,id',
            'is_featured' => 'boolean',
        ];
    }

    public function validated() {

        $validated = parent::validated();
        
        if(empty($validated['is_active'])) {
            $validated['is_active'] = false;
        }
        if(empty($validated['tags'])) {
            $validated['tags'] = [];
        }
        if(empty($validated['categories'])) {
            $validated['categories'] = [];
        }
        if(empty($validated['age_groups'])) {
            $validated['age_groups'] = [];
        }
        if(empty($validated['is_featured'])) {
            $validated['is_featured'] = false;
        }
        
        return $validated;
    }
}