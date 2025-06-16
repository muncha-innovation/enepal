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
use Illuminate\Support\Facades\Cache;
class BusinessController extends Controller
{

public function getBusinesses(Request $request)
{
    $cacheKey = 'businesses:' . md5(json_encode([
        'per_page' => $request->get('per_page', 10),
        'type_id' => $request->get('type_id'),
        'featured' => $request->get('featured'),
        'query' => $request->get('query'),
        'filter' => $request->get('filter', 'latest'),
        'lat' => $request->header('Latitude'),
        'lng' => $request->header('Longitude'),
        'user_id' => auth()->id(),
        'page' => $request->get('page', 1),
        'dd-' => time()
    ]));

    $cacheTTL = 300; // Cache for 5 minutes

    $businesses = Cache::remember($cacheKey, $cacheTTL, function () use ($request) {
        $query = Business::query()
            ->select('businesses.*')
            ->with(['address', 'type']);

        $perPage = $request->get('per_page', 10);
        $typeId = $request->get('type_id');
        $featured = $request->get('featured');
        $keyword = $request->get('query');
        $filter = $request->get('filter', 'latest');

        $lat = $request->header('Latitude');
        $lng = $request->header('Longitude');
        $radius = 1000;

        $primaryAddress = null;
        if (!$lat || !$lng) {
            if (auth()->check()) {
                $primaryAddress = auth()->user()->addresses()
                    ->where('address_type', 'primary')
                    ->first();

                if ($primaryAddress && $primaryAddress->location) {
                    $lat = $primaryAddress->location->getLat();
                    $lng = $primaryAddress->location->getLng();
                }
            }
        }

        if ($lat && $lng) {
            // Calculate bounding box (approx)
            $latDiff = $radius / 111; // ~1 deg latitude = 111 km
            $lngDiff = $radius / (111 * cos(deg2rad($lat)));

            $minLat = $lat - $latDiff;
            $maxLat = $lat + $latDiff;
            $minLng = $lng - $lngDiff;
            $maxLng = $lng + $lngDiff;

            $polygonWKT = "POLYGON(($minLng $minLat, $maxLng $minLat, $maxLng $maxLat, $minLng $maxLat, $minLng $minLat))";

            $query->leftJoin('addresses', function ($join) {
                $join->on('addresses.addressable_id', '=', 'businesses.id')
                    ->where('addresses.addressable_type', '=', Business::class);
            })
            ->selectRaw(
                "ROUND(ST_Distance_Sphere(point(?, ?), addresses.location) / 1000, 2) as distance",
                [$lng, $lat]
            )
            ->whereNotNull('addresses.location')
            ->whereRaw("MBRContains(ST_GeomFromText(?), addresses.location)", [$polygonWKT]);
        } else {
            // If no lat/lng, just join addresses normally without distance
            $query->leftJoin('addresses', function ($join) {
                $join->on('addresses.addressable_id', '=', 'businesses.id')
                    ->where('addresses.addressable_type', '=', Business::class);
            });
        }

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

        switch ($filter) {
            case 'popular':
                $query->withCount('users as followers_count')
                    ->orderBy('followers_count', 'desc');
                break;

            case 'nearyou':
                if (isset($lat, $lng)) {
                    $query->orderBy('distance', 'asc');
                } else {
                    $query->orderBy('businesses.created_at', 'desc');
                }
                break;

            case 'latest':
            default:
                $query->orderBy('businesses.created_at', 'desc');
                break;
        }

        return $query->paginate($perPage);
    });

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
