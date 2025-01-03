<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessController extends Controller
{

    public function getBusinesses(Request $request)
    {
        $typeId = $request->get('type_id');
        $featured = $request->get('featured');
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $businesses = Business::query();
        if ($typeId) {
            $businesses->where('type_id', $typeId);
        }
        if ($featured) {
            $businesses->where('is_featured', true);
        }
        $businesses = $businesses->with(['address', 'type'])->limit($limit)->offset($offset)->get();
        return BusinessResource::collection($businesses);
    }

    public function getBusinessType(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);
        $type = BusinessType::findOrFail($request->id);
        $type->businesses = $type->businesses()->get();
        return response()->json($type);
    }

    
    public function products(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer'
        ]);

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $business = Business::findOrFail($request->business_id);
        $products = $business->products()->limit($limit)->offset($offset)->get();
        return response()->json($products);
    }

    public function notices(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer'
        ]);

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $business = Business::findOrFail($request->business_id);
        $notices = $business->notices()->limit($limit)->offset($offset)->get();
        return response()->json($notices);
    }

    public function galleries(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer'
        ]);

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;

        $business = Business::findOrFail($request->business_id);
        $galleries = $business->galleries()->limit($limit)->offset($offset)->get();
        return response()->json($galleries);
    }
    public function getById($id)
    {
        return new BusinessResource(Business::with(['type', 'address', 'posts', 'products', 'notices'])->findOrFail($id));
    }

    public function followUnfollow($businessId)
    {
        $business = Business::findOrFail($businessId);
        $user = $business->users()->where('user_id', auth()->id())->first();
        if (!$user) {
            $business->users()->attach(auth()->id(), [
                'role' => 'member',
                'position' => 'follower'
            ]);
            return response()->json([
                'message' => 'Business followed successfully'
            ]);
        } else {
            if($user->pivot->role=='member') {
                $business->users()->detach(auth()->id());
                return response()->json([
                    'message' => 'Business unfollowed successfully'
                ]);
            } else {
                return response()->json([
                    'message' => 'You are not allowed to unfollow this business'
                ], 403);
            }
        }
    }

    public function following() {
        $businesses = Business::whereHas('users', function($query) {
            $query->where('user_id', auth()->id());
        })->with(['type'])->get();
        
        return BusinessResource::collection($businesses);
    }
}
