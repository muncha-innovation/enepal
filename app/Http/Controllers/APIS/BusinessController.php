<?php

namespace App\Http\Controllers\APIS;

use App\Enums\SettingKeys;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use App\Notify\NotifyProcess;
use Exception;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            $query->leftJoin('addresses', function ($join) {
                $join->on('addresses.addressable_id', '=', 'businesses.id')
                    ->where('addresses.addressable_type', '=', Business::class);
            })
                ->selectRaw(
                    "
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

                $query->leftJoin('addresses', function ($join) {
                    $join->on('addresses.addressable_id', '=', 'businesses.id')
                        ->where('addresses.addressable_type', '=', Business::class);
                })
                    ->selectRaw(
                        "
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
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('type', function ($q) use ($keyword) {
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
            if ($user->pivot->role == 'member') {
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

    public function following()
    {
        $businesses = Business::whereHas('users', function ($query) {
            $query->where('user_id', auth()->id());
        })->with(['type'])->get();

        return BusinessResource::collection($businesses);
    }

    public function getMyBusinesses(Request $request)
    {
        $query = Business::query()
            ->select('businesses.*')
            ->join('business_user', function ($join) {
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

    public function addBusiness(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type_id' => 'required|integer|exists:business_types,id',
        ]);
        $business = Business::create([
            'name' => $request->name,
            'type_id' => $request->type_id,
            'created_by' => auth()->id()
        ]);
        $business->users()->attach(auth()->id(), ['role' => 'owner']);

        return response()->json([
            'message' => trans('Business added successfully', [], $request->get('lang', 'en')),
            'data' => new BusinessResource($business)
        ]);
    }

    public function addMember(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'role' => 'required|string|in:member,admin',
        ]);

        $business = Business::findOrFail($request->business_id);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $existingUser = $business->users()->where('user_id', $user->id)->first();
            if ($existingUser) {
                return response()->json([
                    'message' => 'User is already a member of this business'
                ], 400);
            }
            $business->users()->detach($user->id);
            $business->users()->attach($user->id, [
                'role' => $request->role,
                'has_joined' => true,
            ]);

            $notify = new NotifyProcess();
            $notify->setTemplate(SettingKeys::EXISTING_MEMBER_OUTSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL)
                ->setUser($user)
                ->withShortCodes([
                    'role' => $request->role,
                    'business_name' => $business->name,
                    'site_name' => config('app.name'),
                    'business_message' => $business->custom_email_message,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ]);
            $notify->send();
            return response()->json([
                'message' => 'Member added successfully',
                'member' => UserResource::make($user),
            ]);
        }
        else {
            $password = \Str::random(8);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone_number,
                'password' => bcrypt($password),
                'force_update_password' => true,
            ]);
            $user->assignRole(User::User);

            $business->users()->attach($user->id, [
                'role' => $request->role,
                'has_joined' => false,
            ]);
            try {
                $notify = new NotifyProcess();
                $notify->setTemplate(SettingKeys::NEW_MEMBER_OUTSIDE_NEPAL_ADDED_TO_BUSINESS_EMAIL)
                    ->setUser($user)
                    ->withShortCodes([
                        'role' => $request->role,
                        'business_name' => $business->name,
                        'site_name' => config('app.name'),
                        'password' => $password,
                        'business_message' => $business->custom_email_message,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                    ]);
                $notify->send();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Member added successfully',
            'member' => UserResource::make($user),
        ]);
    }
}
