<?php

namespace App\Http\Requests;

use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\Cache;
use Google\Cloud\Core\Exception\NotFoundException;
use Google\Cloud\Places\V1\PlacesClient;

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
            'locations.*' => 'string|max:255',
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
        // Process locations to extract country_id, state_id, name, and location
        if (isset($validated['locations'])) {
            $validated['locations'] = array_map(function($location) {
                // Check if location is already an array with full details
                if (is_array($location) && isset($location['place_id'])) {
                    // Get from cache if exists
                    $cacheKey = 'place_details_' . md5($location['place_id']);
                    $cachedLocation = Cache::get($cacheKey);
                    
                    if ($cachedLocation) {
                        return $cachedLocation;
                    }

                    // If not in cache, store the current location data
                    $locationData = [
                        'name' => $location['name'],
                        'location' => $location['location'],
                        'country_id' => $location['country_id'],
                        'state_id' => $location['state_id'],
                        'place_id' => $location['place_id'],
                    ];
                    
                    Cache::put($cacheKey, $locationData, now()->addDays(30));
                    return $locationData;
                }

                // For new locations, fetch from Google Places API
                $placeDetails = $this->getPlaceDetails($location);
                $countryId = $this->getCountryId($placeDetails);
                $stateId = $this->getStateId($countryId, $placeDetails);
                
                $lat = $placeDetails['geometry']['location']['lat'] ?? null;
                $lng = $placeDetails['geometry']['location']['lng'] ?? null;
                
                $locationData = [
                    'name' => $placeDetails['name'] ?? $location,
                    'location' => $lat && $lng ? new Point($lat, $lng) : null,
                    'country_id' => $countryId,
                    'state_id' => $stateId,
                    'place_id' => $location,
                ];

                // Cache the new location data
                Cache::put('place_details_' . md5($location), $locationData, now()->addDays(30));
                
                return $locationData;
            }, $validated['locations']);
        }
        return $validated;
    }

    private function getPlaceDetails($placeId) 
    {
        $cacheKey = 'place_details_api_' . md5($placeId);
        
        // Try to get from cache first
        if ($cachedResult = Cache::get($cacheKey)) {
            return $cachedResult;
        }

        try {
            $apiKey = config('services.google.places.api_key');
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'displayName,formattedAddress,location,addressComponents',
                'Content-Type' => 'application/json',
            ])->get("https://places.googleapis.com/v1/places/{$placeId}");

            if ($response->successful()) {
                $result = $response->json();
                
                // Transform the new API response to match our expected format
                $transformedResult = [
                    'name' => $result['displayName']['text'],
                    'formatted_address' => $result['formattedAddress'],
                    'geometry' => [
                        'location' => [
                            'lat' => $result['location']['latitude'],
                            'lng' => $result['location']['longitude']
                        ]
                    ],
                    'address_components' => array_map(function($component) {
                        return [
                            'long_name' => $component['longText'],
                            'short_name' => $component['shortText'],
                            'types' => $component['types']
                        ];
                    }, $result['addressComponents'])
                ];

                // Cache the API response
                Cache::put($cacheKey, $transformedResult, now()->addDays(30));
                return $transformedResult;
            }
        } catch (\Exception $e) {
            \Log::error('Place Details API Error: ' . $e->getMessage());
        }

        return [];
    }

    private function getCountryId($placeDetails) {
        if (empty($placeDetails['address_components']) || !is_array($placeDetails['address_components'])) {
            return null;
        }
        foreach ($placeDetails['address_components'] as $component) {
            if (in_array('country', $component['types'])) {
                $country = Country::where('code', $component['short_name'])->first();
                return $country ? $country->id : null;
            }
        }
        return null;
    }

    private function getStateId($countryId, $placeDetails) {
        if (!$countryId || empty($placeDetails['address_components']) || !is_array($placeDetails['address_components'])) {
            return null;
        }
        // First try with administrative_area_level_1
        $stateId = $this->findStateByAdministrativeLevel($countryId, $placeDetails, 'administrative_area_level_1');
        
        // If no state found, try with administrative_area_level_2
        if (!$stateId) {
            $stateId = $this->findStateByAdministrativeLevel($countryId, $placeDetails, 'administrative_area_level_2');
        }

        return $stateId;
    }

    private function findStateByAdministrativeLevel($countryId, $placeDetails, $level) {
        if (empty($placeDetails['address_components']) || !is_array($placeDetails['address_components'])) {
            return null;
        }
        foreach ($placeDetails['address_components'] as $component) {
            if (in_array($level, $component['types'])) {
                $possibleStateName = preg_replace('/(Province|State|Region)\s*$/i', '', $component['long_name']);
                $possibleStateName = trim($possibleStateName);
                if (!$possibleStateName) continue;

                $states = State::where('country_id', $countryId)->get();
                $closestMatch = null;
                $minDistance = PHP_INT_MAX;
                $threshold = 3;

                foreach ($states as $state) {
                    $distance = levenshtein(
                        strtolower($possibleStateName),
                        strtolower($state->name)
                    );
                    
                    if ($distance < $minDistance && $distance <= $threshold) {
                        $minDistance = $distance;
                        $closestMatch = $state;
                    }
                }
                if ($closestMatch) {
                    return $closestMatch->id;
                }
            }
        }

        return null;
    }
}