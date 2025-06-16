<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessTypesResource;
use App\Models\BusinessType;
use Illuminate\Support\Facades\Cache;

class BusinessTypesController extends Controller
{
    public function index()
    {
        $cacheKey = 'business_types_all';

        $businessTypes = Cache::remember($cacheKey, 60 * 60 * 24 * 2, function () {
            return BusinessType::all();
        });

        return BusinessTypesResource::collection($businessTypes);
    }

    public function getById($id)
    {
        $cacheKey = "business_type_{$id}";

        $type = Cache::remember($cacheKey, 60 * 60 * 24 * 2, function () use ($id) {
            return BusinessType::findOrFail($id);
        });

        return BusinessTypesResource::make($type);
    }
}
