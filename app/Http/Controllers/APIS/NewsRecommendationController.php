<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Services\NewsRecommendationService;
use Illuminate\Http\Request;

class NewsRecommendationController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $service = new NewsRecommendationService(
            user: $request->user(),
            lat: $validated['latitude'] ?? null,
            lng: $validated['longitude'] ?? null
        );

        $recommendations = $service->getRecommendations(
            limit: $validated['limit'] ?? 20
        );
        return NewsResource::collection($recommendations);
    }
} 