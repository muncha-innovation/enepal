<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Resources\UserPreferenceResource;
use App\Models\Country;
use App\Models\Language;
use App\Models\UserPreference;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    /**
     * Get user preferences
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $preference = $user->preference;
        
        if (!$preference) {
            $preference = new UserPreference(['user_id' => $user->id]);
        }
        
        return response()->json([
            'success' => true,
                'preferences' => new UserPreferenceResource($preference),
                'languages' => Language::all(),
            
        ]);
    }
    
    /**
     * Update user preferences
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'user_type' => 'nullable|string|in:nrn,student,job_seeker',
            'countries' => 'nullable|array',
            'departure_date' => 'nullable|date',
            'study_field' => 'nullable|string',
            'app_language' => 'nullable|string|in:en,ne',
            'known_languages' => 'nullable|array',
            'has_passport' => 'nullable|boolean',
            'passport_expiry' => 'nullable|date|required_if:has_passport,true',
            'receive_notifications' => 'nullable|boolean',
            'show_personalized_content' => 'nullable|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $data = $validator->validated();
        
        $preference = $user->preference;
        
        if (!$preference) {
            $preference = new UserPreference();
            $preference->user_id = $user->id;
        }
        
        foreach ($data as $key => $value) {
            if ($request->has($key)) {
                $preference->$key = $value;
            }
        }
        
        $preference->save();
        
        // Update passport flag in user profile if changed here
        if ($request->has('has_passport') && $user->has_passport !== $request->has_passport) {
            $user->has_passport = $request->has_passport;
            $user->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully',
            'data' => new UserPreferenceResource($preference)
        ]);
    }
    
    /**
     * Update news preferences (toggle categories)
     */
    public function updateNewsPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:news_categories,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = auth()->user();
        $user->toggleNewsPreference($request->category_id);
        
        return response()->json([
            'success' => true,
            'message' => 'News preferences updated',
            'data' => $user->preferredCategories()
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'type' => $category->type
                    ];
                })
        ]);
    }
    
    /**
     * Get all news categories with user preferences
     */
    public function getNewsCategories()
    {
        $user = auth()->user();
        $categories = NewsCategory::orderBy('name')->get();
        $preferredCategories = $user->preferredCategories()->pluck('id')->toArray();
        
        $data = $categories->map(function ($category) use ($preferredCategories) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type,
                'is_preferred' => in_array($category->id, $preferredCategories)
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Update multiple news preferences at once
     */
    public function bulkUpdateNewsPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'required|array',
            'categories.*' => 'exists:news_categories,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = auth()->user();
        $user->preferredCategories()->sync($request->categories);
        
        return response()->json([
            'success' => true,
            'message' => 'News preferences updated',
            'data' => $user->preferredCategories()
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'type' => $category->type
                    ];
                })
        ]);
    }
}