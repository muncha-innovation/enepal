<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Models\BusinessType;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;

class BusinessController extends Controller
{

    public function getBusinesses(Request $request)
    {
        $query = Business::query()
            ->select('businesses.*') // Explicitly select all columns from businesses table
            ->with(['address', 'type']);
        $perPage = $request->get('per_page', 10);
        $typeId = $request->get('type_id');
        $featured = $request->get('featured');
        $keyword = $request->get('query');
        $filter = $request->get('filter', 'latest');
        
        // Handle location and distance calculation
        $lat = $request->header('Latitude');
        $lng = $request->header('Longitude');
        
        if ($lat && $lng) {
            $query->leftJoin('addresses', function($join) {
                $join->on('addresses.addressable_id', '=', 'businesses.id')
                     ->where('addresses.addressable_type', '=', Business::class);
            })
            ->selectRaw("
                ROUND(
                    ST_Distance_Sphere(
                        point(?, ?),
                        addresses.location
                    ) / 1000, 
                2) as distance", 
                [$lng, $lat]
            )
            ->whereNotNull('addresses.location');
        } elseif (auth()->check()) {
            // Use authenticated user's primary address
            $primaryAddress = auth()->user()->addresses()
                ->where('address_type', 'primary')
                ->first();

            if ($primaryAddress && $primaryAddress->location) {
                $userLat = $primaryAddress->location->getLat();
                $userLng = $primaryAddress->location->getLng();
                
                $query->leftJoin('addresses', function($join) {
                    $join->on('addresses.addressable_id', '=', 'businesses.id')
                         ->where('addresses.addressable_type', '=', Business::class);
                })
                ->selectRaw("
                    ROUND(
                        ST_Distance_Sphere(
                            point(?, ?),
                            addresses.location
                        ) / 1000, 
                    2) as distance", 
                    [$userLng, $userLat]
                )
                ->whereNotNull('addresses.location');
            }
        }

        // Apply other filters
        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        if ($featured) {
            $query->where('is_featured', true);
        }

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%")
                  ->orWhereHas('type', function($q) use ($keyword) {
                      $q->where('name', 'LIKE', "%{$keyword}%");
                  });
            });
        }

        // Apply sorting based on filter
        switch ($filter) {
            case 'popular':
                $query->withCount('users as followers_count')
                      ->orderBy('followers_count', 'desc');
                break;
                
            case 'nearyou':
                if (isset($lat, $lng) || (auth()->check() && $primaryAddress && $primaryAddress->location)) {
                    $query->orderBy('distance', 'asc');
                } else {
                    $query->orderBy('businesses.created_at', 'desc'); // Specify table name
                }
                break;
                
            case 'latest':
            default:
                $query->orderBy('businesses.created_at', 'desc'); // Specify table name
                break;
        }

        $businesses = $query->paginate($perPage);

        return response()->json([
            'data' => BusinessResource::collection($businesses),
            'meta' => [
                'current_page' => $businesses->currentPage(),
                'last_page' => $businesses->lastPage(),
                'per_page' => $businesses->perPage(),
                'total' => $businesses->total()
            ]
        ]);
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
        return new BusinessResource(Business::with(['type', 'address', 'posts', 'products', 'notices', 'galleries'])->findOrFail($id));
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

    public function getMyBusinesses(Request $request)
    {
        $query = Business::query()
            ->select('businesses.*')
            ->join('business_user', function($join) {
                $join->on('businesses.id', '=', 'business_user.business_id')
                    ->where('business_user.user_id', '=', auth()->id())
                    ->where('business_user.role', '=', 'owner');
            })
            ->with(['address', 'type']);
            
        $perPage = $request->get('per_page', 10);
        $businesses = $query->paginate($perPage);

        return response()->json([
            'data' => BusinessResource::collection($businesses),
            'meta' => [
                'current_page' => $businesses->currentPage(),
                'last_page' => $businesses->lastPage(),
                'per_page' => $businesses->perPage(),
                'total' => $businesses->total()
            ]
        ]);
    }
}
