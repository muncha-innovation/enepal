<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessController extends Controller
{

    public function getBusinesses(Request $request) {
        $typeId = $request->get('type_id');
        $featured = $request->get('featured');
        $limit = $request->get('limit',10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $businesses = Business::query();
        if($typeId) {
            $businesses->where('type_id', $typeId);
        }
        if($featured) {
            $businesses->where('is_featured', true);
        }
        $businesses = $businesses->limit($limit)->offset($offset)->get();
        return BusinessResource::collection($businesses);
    }

    public function getBusinessType(Request $request) {
        $request->validate([
            'id' => 'required|integer'
        ]);
        $type = BusinessType::findOrFail($request->id);
        $type->businesses = $type->businesses()->get();
        return response()->json($type);
    }

    public function posts(Request $request) {
        $request->validate([
            'business_id' => 'required|integer',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer'
        ]);

        $limit = $request->get('limit',10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $business = Business::findOrFail($request->business_id);
        $posts = $business->posts()->limit($limit)->offset($offset)->get();
        return response()->json($posts);
    }
    public function products(Request $request) {
        $request->validate([
            'business_id' => 'required|integer',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer'
        ]);

        $limit = $request->get('limit',10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $business = Business::findOrFail($request->business_id);
        $products = $business->products()->limit($limit)->offset($offset)->get();
        return response()->json($products);
    }

    public function notices(Request $request) {
        $request->validate([
            'business_id' => 'required|integer',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer'
        ]);

        $limit = $request->get('limit',10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $business = Business::findOrFail($request->business_id);
        $notices = $business->notices()->limit($limit)->offset($offset)->get();
        return response()->json($notices);
    }
    public function featured(Request $request) {
        $request->validate([
            'page'=>'required|integer',
            'limit' => 'sometimes|integer'
        ]);
        $limit = $request->get('limit',10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $featured = Business::where('is_featured',1)->limit($limit)->offset($offset)->get();
        return response()->json([
            'businesses' => $featured
        ]);
    }
}
